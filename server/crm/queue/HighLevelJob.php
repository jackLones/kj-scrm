<?php

	/**
	 * Create by PhpStorm
	 * User: xingchangyu
	 * Date: 2019/10/18
	 * Time: 09:43
	 */

	namespace app\queue;

	use app\models\Article;
	use app\models\Tags;
	use app\models\FansTimeLine;
	use app\models\WxAuthorizeInfo;
	use app\util\MsgUtil;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\models\WxAuthorize;
	use app\models\Fans;
	use app\models\HighLevelPushMsg;
	use callmez\wechat\sdk\Wechat;

	class HighLevelJob extends BaseObject implements JobInterface
	{
		public $high_level_push_msg_id;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->high_level_push_msg_id,'high_level_push_msg_id');
			$highLevel = HighLevelPushMsg::findOne(['id' => $this->high_level_push_msg_id, 'is_del' => 0]);
			try{
				if (empty($highLevel)) {
					return false;
				}
				//公众号是否认证
				$wxAuthorizeInfo = WxAuthorizeInfo::findOne(['author_id' => $highLevel->author_id]);
				if (empty($wxAuthorizeInfo) || !in_array($wxAuthorizeInfo->verify_type_info, [0, 3, 4, 5])) {
					$highLevel->status     = 2;
					$highLevel->error_code = 61052;//公众号未认证
					$highLevel->update();

					return false;
				}

				$wxAuthorize = WxAuthorize::getTokenInfo($highLevel->author->authorizer_appid, false, true);
				if (empty($wxAuthorize)) {
					$highLevel->status    = 2;
					$highLevel->error_msg = '获取token失败';
					$highLevel->update();

					return false;
				}

				$wechat = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $highLevel->author->authorizer_appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);

				$push_rule = json_decode($highLevel->push_rule, true);
				$sex       = $push_rule['sex'];
				$stime     = $push_rule['stime'];
				$etime     = $push_rule['etime'];
				$province  = $push_rule['province'];
				$city      = $push_rule['city'];
				$tag_ids   = $push_rule['tag_ids'];
				$send_type = $push_rule['send_type'];
				$is_custom = $push_rule['is_custom'];
				if ($send_type == 2 && strtotime($highLevel->push_time) > time()) {
					return false;
				}
				$typeArr  = [
					1 => 'text',
					2 => 'image',
					3 => 'voice',
					4 => 'mpvideo',
					5 => 'mpnews',
				];
				$typeName = $typeArr[$highLevel->msg_type];
				if ($highLevel->msg_type != 1) {
					$attachment = $highLevel->attachment;
					if(empty($attachment) || $attachment->status == 0){
						$highLevel->status     = 2;
						$highLevel->queue_id   = 0;
						$highLevel->error_code = 0;
						$highLevel->error_msg  = '素材已被删除，请重新创建';
						$highLevel->update();
						return false;
					}

					$materialInfo = $highLevel->material;
					if(!empty($materialInfo)){
						if($highLevel->msg_type != 5){
							MsgUtil::checkNeedReload($materialInfo);
						}
						$content = $materialInfo->media_id;
						if($highLevel->msg_type == 4){
							$content = HighLevelPushMsg::getVideoMediaId($highLevel->author_id,$materialInfo->media_id,$materialInfo->title,$materialInfo->introduction);
						}
					}elseif($highLevel->msg_type == 5){
						$appPath = \Yii::getAlias('@app');
						if(!empty($attachment->material_id) && !empty($attachment->material->article_sort)){
							$articles = [];
							$article = Article::find()->alias('a');
							$article = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
							$articleIds = explode(',', $attachment->material->article_sort);
							foreach ($articleIds as $aIds) {
								$artInfo = $article->where(['a.id' => $aIds])->select('a.title,a.author,a.digest,a.wx_content,a.show_cover_pic,m.local_path,a.content_source_url')->asArray()->one();

								$result = $wechat->uploadMedia($appPath . $artInfo['local_path'],'image');
								if (empty($result['media_id'])){
									$highLevel->status     = 2;
									$highLevel->queue_id   = 0;
									$highLevel->error_code = $result['errcode'];
									$highLevel->error_msg  = $result['errmsg'];
									$highLevel->update();
									return false;
								}
								$thumb_media_id = $result['media_id'];
								$temp = [
									'thumb_media_id'     => $thumb_media_id,
									'title'              => $artInfo['title'],
									'author'             => !empty($artInfo['author']) ? $artInfo['author'] : '',
									'digest'             => $artInfo['digest'],
									'content'            => $artInfo['wx_content'],
									'content_source_url' => $artInfo['content_source_url'],
									'show_cover_pic'     => $artInfo['show_cover_pic'],
								];
								array_push($articles, $temp);
							}
						}else{
							$result = $wechat->uploadMedia($appPath . $attachment->local_path,'image');
							if (empty($result['media_id'])){
								$highLevel->status     = 2;
								$highLevel->queue_id   = 0;
								$highLevel->error_code = $result['errcode'];
								$highLevel->error_msg  = $result['errmsg'];
								$highLevel->update();
								return false;
							}
							$thumb_media_id = $result['media_id'];

							$site_url   = \Yii::$app->params['site_url'];
							$wx_content = $attachment->image_text;
							if (!empty($wx_content)) {
								//图文消息内的图片换取微信URL
								$pattern = "/<img.*?src=['|\"](.*?(?:\.(?:png|jpg)))['|\"].*?[\/]?>/i";
								preg_match_all($pattern, $wx_content, $match);
								$appUrl = \Yii::getAlias('@app');
								if (!empty($match[1])) {
									foreach ($match[1] as $filePath) {
										if (strpos($filePath, 'http://mmbiz.qpic.cn') === false) {
											$filePathUrl = str_replace($site_url, $appUrl, $filePath);
											$res         = $wechat->uploadImg($filePathUrl);
											if (isset($res['url']) && !empty($res['url'])) {
												$wx_content = str_replace($filePath, $res['url'], $wx_content);
											}
										}
									}
								}
							}

							$articles = [
								[
									'thumb_media_id'     => $thumb_media_id,
									'title'              => $attachment->file_name,
									'author'             => !empty($attachment->author) ? $attachment->author : '',
									'digest'             => $attachment->content,
									'content'            => $wx_content,
									'content_source_url' => $attachment->jump_url,
									'show_cover_pic'     => $attachment->show_cover_pic,
								]
							];
						}
						\Yii::error($articles,'$articles');
						$result = $wechat->uploadArticles($articles);
						\Yii::error($result,'uploadArticles');
						if(!empty($result['media_id'])){
							$content = $result['media_id'];
						}else{
							$highLevel->status     = 2;
							$highLevel->queue_id   = 0;
							$highLevel->error_code = $result['errcode'];
							$highLevel->error_msg  = $result['errmsg'];
							$highLevel->update();
							return false;
						}
					}
				}else{
					$content  = rawurldecode($highLevel->content);
				}
				$msgId         = [];
				$error_code    = 0;
				$error_msg     = '';
				$client_msg_id = '';
				//发送类型
				$push_type = $highLevel->push_type;
				if ($wxAuthorizeInfo->service_type_info == 2) {//服务号
					if ($push_type == 1) {
						$fans = Fans::find()->alias('f');
						$fans = $fans->where(['f.author_id' => $highLevel->author_id,'f.subscribe'=>1]);
						$fans = $fans->select('f.openid,f.id');
						if (!empty($sex)) {
							if($sex == 3){
								$sex = 0;
							}
							$fans = $fans->andWhere(['f.sex' => $sex]);
						}
						if (!empty($stime)) {
							$fans = $fans->andWhere(['>=', 'f.subscribe_time', $stime]);
						}
						if (!empty($etime)) {
							$fans = $fans->andWhere(['<=', 'f.subscribe_time', $etime]);
						}
						if (!empty($province)) {
							$fans = $fans->andWhere(['f.province' => $province]);
						}
						if(!empty($is_custom)){
							if ($is_custom == 1) {
								$fans = $fans->andWhere(['!=', 'f.external_userid', '']);
							} else {
								$fans = $fans->andWhere(['f.external_userid' => NULL]);
							}
						}
						if (!empty($city)) {
							$fans = $fans->andWhere(['f.city' => $city]);
						}
						if (!empty($tag_ids)) {
							$tagIds = explode(',', $tag_ids);
							$fans   = $fans->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`')->andWhere(['and', ['f.author_id' => $highLevel->author_id], ['in', 'ft.tags_id', $tagIds]])->groupBy('f.id');
						}
						$count = $fans->count();
						if (empty($count)) {
							$highLevel->status     = 2;
							$highLevel->queue_id   = 0;
							$highLevel->error_code = $error_code;
							$highLevel->error_msg  = '粉丝数量为0请更新粉丝数据';
							$highLevel->update();

							return false;
						}
						$pageSize = 10000;
						$continue = true;
						while ($continue) {
							$rest = $count % $pageSize;
							if ($rest > 1 || $rest == 0) {
								break;
							}
							$pageSize--;
						}
						$page = ceil($count / $pageSize);
						for ($i = 0; $i < $page; $i++) {
							$offset   = $i * $pageSize;
							$fansData = $fans->limit($pageSize)->offset($offset)->asArray()->all();
							$openids  = array_column($fansData, 'openid');
							$fansIds  = array_column($fansData, 'id');
							$touser   = [
								'touser' => $openids
							];
							$result   = $wechat->sendArticles($touser, $content, $typeName);
							if ($result['errcode'] == 0) {
								array_push($msgId, $result['msg_id']);
							} else {
								$error_code = $result['errcode'];
								$error_msg  = $result['errmsg'];
								if (!empty($result['msg_id'])) {
									$client_msg_id = $result['msg_id'];
								}
							}
							foreach ($fansIds as $fans_id) {
								//插入粉丝轨迹
								FansTimeLine::create($fans_id, 'group', time(),0,0,$typeName);
							}
							//群发接口每分钟限制请求60次
							if ($page > 1) {
								sleep(1);
							}
						}
					} elseif ($push_type == 2) {
						//全部粉丝
						$fans  = Fans::find()->where(['author_id' => $highLevel->author_id])->select('id,openid');
						$count = $fans->count();
						if (empty($count)) {
							$highLevel->status     = 2;
							$highLevel->queue_id   = 0;
							$highLevel->error_code = $error_code;
							$highLevel->error_msg  = '粉丝数量为0请更新粉丝数据';
							$highLevel->update();

							return false;
						}
						$pageSize = 10000;
						$continue = true;
						while ($continue) {
							$rest = $count % $pageSize;
							if ($rest > 1 || $rest == 0) {
								break;
							}
							$pageSize--;
						}
						$page = ceil($count / $pageSize);
						for ($i = 0; $i < $page; $i++) {
							$offset   = $i * $pageSize;
							$fansData = $fans->limit($pageSize)->offset($offset)->asArray()->all();
							$openids  = array_column($fansData, 'openid');
							$fansIds  = array_column($fansData, 'id');
							$touser   = [
								'touser' => $openids
							];
							$result   = $wechat->sendArticles($touser, $content, $typeName);
							if ($result['errcode'] == 0) {
								array_push($msgId, $result['msg_id']);
							} else {
								$error_code = $result['errcode'];
								$error_msg  = $result['errmsg'];
								if (!empty($result['msg_id'])) {
									$client_msg_id = $result['msg_id'];
								}
							}
							foreach ($fansIds as $fans_id) {
								//插入粉丝轨迹
								FansTimeLine::create($fans_id, 'group', time(),0,0,$typeName);
							}
							//群发接口每分钟限制请求60次
							if ($page > 1) {
								sleep(1);
							}
						}

					} elseif ($push_type == 3) {
						//指定粉丝
						$openidArr = explode(';', $push_rule['openids']);
						if (!empty($openidArr)) {
							$touser = [
								'touser' => $openidArr
							];
							$result = $wechat->sendArticles($touser, $content, $typeName);
							if ($result['errcode'] == 0) {
								array_push($msgId, $result['msg_id']);
							} else {
								$error_code = $result['errcode'];
								$error_msg  = $result['errmsg'];
								if (!empty($result['msg_id'])) {
									$client_msg_id = $result['msg_id'];
								}
							}
							foreach ($openidArr as $openid) {
								$fans = Fans::findOne(['openid' => $openid]);
								//插入粉丝轨迹
								FansTimeLine::create($fans->id, 'group', time(),0,0,$typeName);
							}
						}
					}
				} else {//订阅号
					if ($push_type == 1) {
						$tag_id = intval($tag_ids);
						$tagInfo = Tags::findOne($tag_id);
						$filter = [
							'filter' => [
								'is_to_all' => false,
								'tag_id'    => $tagInfo->tag_id
							]
						];
					} elseif ($push_type == 2) {
						$filter = [
							'filter' => [
								'is_to_all' => true
							]
						];
					} else {
						return false;
					}
					if ($highLevel->msg_type == 5) {
						$filter['send_ignore_reprint'] = $highLevel->continue;
					}
					$result = $wechat->sendArticles($filter, $content, $typeName);
					if ($result['errcode'] == 0) {
						array_push($msgId, $result['msg_id']);
					} else {
						$error_code = $result['errcode'];
						$error_msg  = $result['errmsg'];
					}
				}
				if (!empty($msgId)) {
					$highLevel->status   = 1;
					$highLevel->queue_id = 0;
					$highLevel->msg_id   = implode(',', $msgId);
					$highLevel->update();
				} else {
					$highLevel->status     = 2;
					$highLevel->queue_id   = 0;
					$highLevel->error_code = $error_code;
					$highLevel->error_msg  = $error_msg;
					$highLevel->msg_id     = $client_msg_id;
					$highLevel->update();
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'high_level_push_msg');
				$highLevel->status     = 2;
				$highLevel->queue_id   = 0;
				$highLevel->error_code = $e->getCode();
				$highLevel->error_msg  = '发送失败';
				$highLevel->update();
			}
		}
	}