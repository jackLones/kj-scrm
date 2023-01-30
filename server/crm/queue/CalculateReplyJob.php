<?php
	/**
	 * 重新计算关注和收到消息的推送时间
	 * User: wangpan
	 * Date: 2019/12/26
	 * Time: 15:53
	 */

	namespace app\queue;

	use app\models\InteractReply;
	use app\models\InteractReplyDetail;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\models\AutoReply;
	use app\queue\InterReplyJob;

	class CalculateReplyJob extends BaseObject implements JobInterface
	{
		public $inter_id; //pig_interact_reply表的id
		public $type; //1 关注回复 2 消息回复

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			\Yii::error($this->inter_id, 'inter_id');
			\Yii::error($this->type, 'type');
			try {
				$inter_reply_detail_id = [];
				$data                  = [];
				$interReply            = InteractReply::findOne(['id' => $this->inter_id]);
				$detail                = InteractReplyDetail::find()->andWhere(['inter_id' => $this->inter_id, 'type' => $this->type])->andWhere(['>', 'queue_id', 0]);
				\Yii::error($detail->createCommand()->getRawSql(), 'sql');
				$detail1 = $detail->all();
				if (!empty($detail1)) {
					foreach ($detail1 as $v) {
						\Yii::$app->queue->remove($v->id);
						array_push($inter_reply_detail_id, $v->id);
					}
				}
				$detail2 = $detail->groupBy('openid')->all();
				if (!empty($detail2)) {
					foreach ($detail2 as $v) {
//						if ($this->type == 2) {
//							$start_time = date("Y-m-d", time());
//							$end_time   = date("Y-m-d", time()) . ' 23:59:59';
//							$detail     = InteractReplyDetail::find()->andWhere(['inter_id' => $this->inter_id, 'status' => 0, 'openid' => $v->openid])->andFilterWhere(['between', 'create_time', $start_time, $end_time])->one();
//							if (!empty($detail)) {
//								return false;
//							}
//						}
						$data['author_id']  = $v->author_id;
						$data['openid']     = $v->openid;
						$data['inter_time'] = $v->inter_time;
						$data['type']       = $v->type;
						$no_send_type       = $interReply->no_send_type;
						$no_send_time       = $interReply->no_send_time;
						$this->sendData($data, $no_send_type, $no_send_time);
					}
				}
				\Yii::error($inter_reply_detail_id,'$inter_reply_detail_id');
				if (!empty($inter_reply_detail_id)) {
					InteractReplyDetail::deleteAll(['id' => $inter_reply_detail_id]);
				}

			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'CalculateReplyJob');
			}
		}

		private function sendData ($data, $no_send_type, $no_send_time)
		{
			$inter_time = $data['inter_time'];
			$auto_eve   = AutoReply::find()->andWhere(['inter_id' => $this->inter_id])->asArray()->all();
			foreach ($auto_eve as $reply) {
				$time_json                 = $reply['time_json'];
				$time_json                 = json_decode($time_json, true);
				$reply_detail              = [];
				$reply_detail['type']      = $data['type'];
				$reply_detail['author_id'] = $data['author_id'];
				$reply_detail['inter_id']  = $this->inter_id;
				$reply_detail['openid']    = $data['openid'];
				$reply_detail['time']      = $data['inter_time'];
				$reply_detail['auto_id']   = $reply['id'];
				if (!empty(intval($time_json[0])) || !empty(intval($time_json[1]))) {
					//非立即发送
					if (intval($time_json[0]) > 0 && intval($time_json[1]) == 0) {
						$time = intval($time_json[0]) * 3600;
					} elseif (intval($time_json[0]) == 0 && intval($time_json[1]) > 0) {
						$time = $time_json[1] * 60;
					} elseif (intval($time_json[0]) > 0 && intval($time_json[1]) > 0) {
						$time = intval($time_json[0]) * 3600 + intval($time_json[1]) * 60;
					}
					if ($inter_time + $time < time()) {
						continue;
					}
					if ($no_send_type == 1) {
						//不推送时间段为开
						$no_send_time_1 = json_decode($no_send_time);
						$date1          = date("Y-m-d", time());
						$stime1         = $date1 . ' ' . $no_send_time_1[0];
						$stime2         = $date1 . ' ' . $no_send_time_1[1];
						//互动时间+修改后的时间处于安静模式下
						if ($inter_time + $time >= strtotime($stime1) && $inter_time + $time <= strtotime($stime2)) {
							$second = strtotime($stime2) - time() + 60;
							//返回推送明细的id
							$inter_id = InteractReplyDetail::create($reply_detail, 3);
							$jobId    = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
								'author_id'      => $data['author_id'],
								'openid'         => $data['openid'],
								'auto_id'        => $reply['id'],
								'type'           => $data['type'],
								'inter_id'       => $inter_id,
								'inter_reply_id' => $this->inter_id,
							]));
							if ($jobId) {
								$tmp = InteractReplyDetail::findOne(['id' => $inter_id]);
								if ($tmp) {
									$queue_id = $tmp->queue_id;
									if (!empty($queue_id)) {
										\Yii::$app->queue->remove($queue_id);
									}
									$tmp->queue_id = $jobId;
									$tmp->save();
								}
							}

						} else {
							$second = $inter_time + $time - time();
							\Yii::error($second, '$second');
							//返回推送明细的id
							$inter_id = InteractReplyDetail::create($reply_detail, 3);
							$jobId    = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
								'author_id'      => $data['author_id'],
								'openid'         => $data['openid'],
								'auto_id'        => $reply['id'],
								'type'           => $data['type'],
								'inter_id'       => $inter_id,
								'inter_reply_id' => $this->inter_id,
							]));
							if ($jobId) {
								$tmp = InteractReplyDetail::findOne(['id' => $inter_id]);
								if ($tmp) {
									$queue_id = $tmp->queue_id;
									if (!empty($queue_id)) {
										\Yii::$app->queue->remove($queue_id);
									}
									$tmp->queue_id = $jobId;
									$tmp->save();
								}
							}
						}
					} else {
						//不推送时间段为关
						$second = $inter_time + $time - time();
						//返回推送明细的id
						$inter_id = InteractReplyDetail::create($reply_detail, 3);
						$jobId    = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
							'author_id'      => $data['author_id'],
							'openid'         => $data['openid'],
							'auto_id'        => $reply['id'],
							'type'           => $data['type'],
							'inter_id'       => $inter_id,
							'inter_reply_id' => $this->inter_id,
						]));
						if ($jobId) {
							$tmp = InteractReplyDetail::findOne(['id' => $inter_id]);
							if ($tmp) {
								$queue_id = $tmp->queue_id;
								if (!empty($queue_id)) {
									\Yii::$app->queue->remove($queue_id);
								}
								$tmp->queue_id = $jobId;
								$tmp->save();
							}
						}

					}
				}
			}
		}












	}
