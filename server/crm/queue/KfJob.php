<?php
	/**
	 * 客服消息发送
	 * User: xingchangyu
	 * Date: 2019/10/24
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\models\Attachment;
	use app\models\Fans;
	use app\models\KfPushMsg;
	use app\models\FansTimeLine;
	use app\util\MsgUtil;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class KfJob extends BaseObject implements JobInterface
	{
		public $kf_push_msg_id;
		public $limit = 1000;
		public $offset = 0;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->kf_push_msg_id, 'kf_push_msg_id');
			\Yii::error($this->offset, 'offset');
			$kfData = KfPushMsg::findOne(['id' => $this->kf_push_msg_id, 'is_del' => 0]);
			try{
				if (empty($kfData)) {
					return false;
				}
				$appid     = $kfData->author->authorizer_appid;
				$push_rule = json_decode($kfData->push_rule, true);
				$sex       = $push_rule['sex'];
				$stime     = $push_rule['stime'];
				$etime     = $push_rule['etime'];
				$province  = $push_rule['province'];
				$city      = $push_rule['city'];
				$tag_ids   = $push_rule['tag_ids'];
				$send_type = $push_rule['send_type'];
				if ($send_type == 2 && strtotime($kfData->push_time) > time()) {
					\Yii::error(strtotime($kfData->push_time), 'push_time');
					\Yii::error(time(), 'time');
					return false;
				}
				$fans_num  = 0;
				$last_time = time() - 172800;
				//发送类型
				$push_type = $kfData->push_type;
				if ($push_type == 1) {//根据条件
					$fans = Fans::find()->alias('f');
					$fans = $fans->where(['f.author_id' => $kfData->author_id]);
					$fans = $fans->andWhere(['>', 'f.last_time', $last_time]);//活跃时间在48小时之内
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
					if (!empty($city)) {
						$fans = $fans->andWhere(['f.city' => $city]);
					}
					if (!empty($tag_ids)) {
						$tagIds = explode(',', $tag_ids);
						$fans   = $fans->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`')->andWhere(['and', ['f.author_id' => $kfData->author_id], ['in', 'ft.tags_id', $tagIds]])->groupBy('f.id');
					}
					$fans = $fans->select('id,nickname,openid');
					$fans = $fans->limit($this->limit)->offset($this->offset)->asArray()->all();
				} elseif ($push_type == 2) {//全部粉丝
					//全部粉丝
					$fans = Fans::find()->where(['author_id' => $kfData->author_id])->andWhere(['>', 'last_time', $last_time])->select('id,nickname,openid');
					$fans = $fans->limit($this->limit)->offset($this->offset)->asArray()->all();
				}
				//地址
				$site_url = \Yii::$app->params['site_url'];
				//发送
				$error_code = 0;
				$error_msg  = '';
				if(empty($fans)){
					$error_msg  = '粉丝数量为0请更新粉丝数据';
				}

				if(!empty($kfData->attachment_id)){
					$attachment = Attachment::findOne($kfData->attachment_id);
					if($attachment->status == 0){
						$error_msg  = '素材已被删除，请重新创建';
						$fans = [];
					}
				}
				$count = 0;
				foreach ($fans as $k=>$v) {
					$count++;
					if ($k == 0) {
						$kfData->status = 3;//发送中
						$kfData->save();
					}
					$msgContent = [];
					if ($kfData->msg_type == 1) {
						$kfData->content = rawurldecode($kfData->content);
						if (strpos($kfData->content, '{nickname}') !== false) {
							$nickname           = $v['nickname'];
							$msgContent['text'] = str_replace("{nickname}", $nickname, $kfData->content);
						} else {
							$msgContent['text'] = $kfData->content;
						}
					} elseif ($kfData->msg_type == 2) {
						$msgContent['media_id'] = $kfData->material_id;
					} elseif ($kfData->msg_type == 3) {
						$msgContent['media_id'] = $kfData->material_id;
					} elseif ($kfData->msg_type == 4) {
						$msgContent['media_id']       = $kfData->material_id;
						$msgContent['thumb_media_id'] = '';
					} elseif ($kfData->msg_type == 5) {
						if (!empty($kfData->material_id)) {
							$msgContent['media_id'] = $kfData->material_id;
						} else {
							$msgContent = [
								"title"       => $kfData->title,
								"description" => $kfData->digest,
								"url"         => $kfData->content_url,
								"pic_url"     => $site_url . $kfData->cover_url
							];
							if (!empty($kfData->attachment_id)) {
								$msgContent = [
									"title"       => $attachment->file_name,
									"description" => $attachment->content,
									"url"         => $attachment->jump_url,
									"pic_url"     => $site_url . $attachment->local_path
								];
							}
						}
					}
					try {
						$result = MsgUtil::send($appid, $v['openid'], $kfData->msg_type, $msgContent);
					} catch (\Exception $e) {
						\Yii::error($e->getMessage(), 'getMessage');
						continue;
					}
					\Yii::error($result,'$result');
					if ($result['errcode'] == 0) {
						//插入粉丝轨迹
						FansTimeLine::create($v['id'], 'kefu', time(), 0, 0, $kfData->msg_type);
						$fans_num++;
					} else {
						$error_code = $result['errcode'];
						$error_msg  = $result['errmsg'];
					}
				}

				\Yii::error($kfData->fans_num,'fans_num');
				if($count < $this->limit){
					if(empty($fans_num) && empty($kfData->fans_num)){
						//发送失败
						$kfData->status     = 2;
						$kfData->queue_id   = 0;
						$kfData->error_code = $error_code;
						$kfData->error_msg  = $error_msg;
						$kfData->save();
					}else{
						//更新发送成功粉丝数
						$kfData->status   = 1;
						$kfData->fans_num += $fans_num;
						$kfData->queue_id = 0;
						$kfData->save();
					}
				}else{
					$kfData->fans_num += $fans_num;
					$kfData->queue_id = 0;
					$kfData->save();
					//再次插入队列
					\Yii::$app->queue->push(new KfJob([
						'kf_push_msg_id' => $this->kf_push_msg_id,
						'offset'         => $this->offset + $this->limit
					]));
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'kf_push_msg');
				$kfData->status     = 2;
				$kfData->queue_id   = 0;
				$kfData->error_code = $e->getCode();
				$kfData->error_msg  = '发送失败';
				$kfData->save();
			}

		}
	}