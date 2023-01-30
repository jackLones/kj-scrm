<?php
	/**
	 * 智能互动消息发送
	 * User: wangpan
	 * Date: 2019/11/17
	 * Time: 17:03
	 */

	namespace app\queue;

	use app\models\Attachment;
	use app\models\Fans;
	use app\models\FansTimeLine;
	use app\models\Material;
	use app\models\InteractReply;
	use app\models\InteractReplyDetail;
	use app\models\WxAuthorize;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\models\AutoReply;
	use callmez\wechat\sdk\Wechat;
	use app\util\DateUtil;

	class InterReplyJob extends BaseObject implements JobInterface
	{
		public $author_id;
		public $openid;
		public $auto_id;
		public $inter_id;
		public $type; //1 关注回复 2 消息回复
		public $inter_reply_id;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			try {
				$source = 1;
				if($this->type==2){
					$source = 3;
				}
				\Yii::error($this->inter_id,'$this->inter_id');
				\Yii::error($this->inter_reply_id,'inter_reply_id');
				$reply = InteractReply::find()->andWhere(['id' => $this->inter_reply_id, 'status' => 1])->one();
				$inter = InteractReplyDetail::findOne(['id' => $this->inter_id]);
				if (empty($inter)) {
					return false;
				}
				$queue_id = $inter->queue_id;
				if (empty($reply)) {
					if (!empty($queue_id)) {
						\Yii::$app->queue->remove($queue_id);
					}

					return false;
				} else {
					if ($reply['reply_type'] == 1 || $reply['reply_type'] == 3) {
						$stime2 = strtotime($reply['end_time']);
						if (time() > $stime2) {
							//不在指定日期范围内的不再推送
							if (!empty($queue_id)) {
								\Yii::$app->queue->remove($queue_id);
							}

							return false;
						}

					}
				}
				$error_code = 0;
				$error_msg  = '';
				$author_id  = $this->author_id;
				$auto       = AutoReply::findOne(['id' => $this->auto_id]);
				if (!empty($auto)) {
					$wxAuthorize = WxAuthorize::getTokenInfo($auto->author->authorizer_appid, false, true);
				}else{
					return false;
				}
				$replyList  = InteractReply::getReplyList($author_id, $this->openid, $auto);
				$toUserName = $this->openid;
				$wechat     = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $auto->author->authorizer_appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);
				$fans       = Fans::findOne(['openid' => $this->openid, 'author_id' => $author_id]);
				$flag = 0;
				$json = json_decode($auto->time_json,true);
				$result = [];
				$result['errcode'] = 0;
				$result['errmsg'] = '';
				if(!empty($replyList)){
					foreach ($replyList as $rv) {
						if ($rv['type'] == 1) {
							$result = $wechat->sendText($toUserName, $rv['content']);
							if ($result['errcode'] == 0) {
								FansTimeLine::create($fans->id, 'text', time(), 0, $source, $auto->time_json);
								$flag = 1;
							}
						} elseif ($rv['type'] == 2) {
							$status = $this->getMaterialIdStatus($rv['material_id']);
							if($status==1){
								$result = $wechat->sendImage($toUserName, $rv['content']);
								if ($result['errcode'] == 0) {
									FansTimeLine::create($fans->id, 'image', time(), 0, $source, $auto->time_json);
									$flag = 1;
								}
							}else {
								$error_code = '99999';
								$error_msg  = '素材已被删除，请重新创建';
							}
						} elseif ($rv['type'] == 3) {
							$status = $this->getMaterialIdStatus($rv['material_id']);
							if($status==1){
								$result = $wechat->sendVoice($toUserName, $rv['content']);
								if ($result['errcode'] == 0) {
									FansTimeLine::create($fans->id, 'voice', time(), 0, $source, $auto->time_json);
									$flag = 1;
								}
							}else {
								$error_code = '99999';
								$error_msg  = '素材已被删除，请重新创建';
							}
						} elseif ($rv['type'] == 4) {
							$status = $this->getMaterialIdStatus($rv['material_id']);
							if ($status == 1) {
								$title      = $rv['title'];
								$attachment = Attachment::findOne($rv['material_id']);
								if (!empty($attachment->file_name)) {
									$title = $attachment->file_name;
								}
								$result = $wechat->sendVideo($toUserName, $rv['content'], $rv['content'], $title, $rv['digest']);
								if ($result['errcode'] == 0) {
									FansTimeLine::create($fans->id, 'video', time(), 0, $source, $auto->time_json);
									$flag = 1;
								}
							} else {
								$error_code = '99999';
								$error_msg  = '素材已被删除，请重新创建';
							}
						} elseif ($rv['type'] == 5) {
							$articles = [];
							//发送客服图文消息时，只能发送一条
							$articleCount = 0;
							foreach ($rv['content'] as $rvv) {
								if ($articleCount > 8) {
									continue;
								}
								$articles[] = [
									"title"       => $rvv['title'],
									"description" => $rvv['digest'],
									"url"         => $rvv['content_url'],
									"picurl"      => $rvv['pic_url']
								];
								$articleCount++;
								$status = $this->getMaterialIdStatus($rvv['material_id']);
							}
							if ($status == 1) {
								$count = count($articles);
								if ($count > 1) {
									if ($json[0] == 0 && $json[1] == 0) {
										$news   = [];
										$random = rand(0, $count - 1);
										$news[] = $articles[$random];
										$result = $wechat->sendNews($toUserName, $news);
									} else {
										for ($i = 0; $i < $count; $i++) {
											$news   = [];
											$news[] = $articles[$i];
											$result = $wechat->sendNews($toUserName, $news);
										}
									}
								} else {
									$result = $wechat->sendNews($toUserName, $articles);
								}
								if ($result['errcode'] == 0) {
									$flag = 1;
									FansTimeLine::create($fans->id, 'news', time(), 0, $source, $auto->time_json);
								}
							} else {
								$error_code = '99999';
								$error_msg  = '素材已被删除，请重新创建';
							}
						}
					}
				}else{
					$error_msg = '当前素材不存在';
				}
				if ($result['errcode'] != 0) {
					$error_code = $result['errcode'];
					$error_msg  = $result['errmsg'];
				}
				if($flag == 1){
					$error_code = 0;
					$error_msg = '';
				}
				$inter->push_time  = DateUtil::getCurrentTime();
				$inter->error_code = $error_code;
				$inter->error_msg  = $error_msg;
				$inter->queue_id   = 0;
				if($flag == 1){
					$inter->status = 0;
				}else{
					$inter->status = 1;
				}
				$inter->save();
				//插入统计数据
				InteractReply::insertStatisc($inter->inter_id, $author_id);
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'InterReplyJob_message');
			}
		}

		//获取素材删除状态
		private function getMaterialIdStatus($attachment_id){
			$status = 0;
			$material = Attachment::findOne($attachment_id);
			if ($material->status == 1) {
				$status = 1;
			}
			return $status;
		}
	}
