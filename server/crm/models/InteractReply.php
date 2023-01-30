<?php

	namespace app\models;

	use Yii;
	use app\util\DateUtil;
	use app\queue\InterReplyJob;
	use app\util\WxConstUtil;
	use app\util\SUtils;
	use app\util\MsgUtil;
	use callmez\wechat\sdk\Wechat;
	use yii\db\Expression;
	use yii\helpers\Json;

	/**
	 * This is the model class for table "{{%interact_reply}}".
	 *
	 * @property int                   $id
	 * @property int                   $author_id    公众号ID
	 * @property int                   $type         1 关注回复 2 消息回复
	 * @property string                $title        名称
	 * @property int                   $reply_type   1今天 2每天 3指定日期
	 * @property string                $start_time   开始时间
	 * @property string                $end_time     结束时间
	 * @property int                   $no_send_type 1不推送 2推送
	 * @property string                $no_send_time 不推送时间段
	 * @property string                $create_time  创建时间
	 * @property int                   $status       是否开启，0代表未开启，1代表开启
	 * @property int                   $push_num     推送人数
	 * @property string                $close_time   关闭时间
	 * @property string                $update_time  修改时间
	 *
	 * @property AutoReply[]           $autoReplies
	 * @property WxAuthorize           $author
	 * @property InteractReplyDetail[] $interactReplyDetails
	 * @property InteractStatistic[]   $interactStatistics
	 */
	class InteractReply extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%interact_reply}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['author_id', 'type', 'reply_type', 'no_send_type', 'status', 'push_num'], 'integer'],
				[['start_time', 'end_time', 'create_time', 'close_time', 'update_time'], 'safe'],
				[['title'], 'string', 'max' => 64],
				[['no_send_time'], 'string', 'max' => 32],
				[['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => WxAuthorize::className(), 'targetAttribute' => ['author_id' => 'author_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'           => Yii::t('app', 'ID'),
				'author_id'    => Yii::t('app', '公众号ID'),
				'type'         => Yii::t('app', '1 关注回复 2 消息回复'),
				'title'        => Yii::t('app', '名称'),
				'reply_type'   => Yii::t('app', '1今天 2每天 3指定日期'),
				'start_time'   => Yii::t('app', '开始时间'),
				'end_time'     => Yii::t('app', '结束时间'),
				'no_send_type' => Yii::t('app', '1不推送 2推送'),
				'no_send_time' => Yii::t('app', '不推送时间段'),
				'create_time'  => Yii::t('app', '创建时间'),
				'status'       => Yii::t('app', '是否开启，0代表未开启，1代表开启'),
				'push_num'     => Yii::t('app', '推送人数'),
				'close_time'   => Yii::t('app', '关闭时间'),
				'update_time'  => Yii::t('app', '修改时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAutoReplies ()
		{
			return $this->hasMany(AutoReply::className(), ['inter_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAuthor ()
		{
			return $this->hasOne(WxAuthorize::className(), ['author_id' => 'author_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInteractReplyDetails ()
		{
			return $this->hasMany(InteractReplyDetail::className(), ['inter_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInteractStatistics ()
		{
			return $this->hasMany(InteractStatistic::className(), ['inter_id' => 'id']);
		}

		public function dumpData ()
		{
			$result = [
				'id'       => $this->id,
				'key'      => $this->id,
				'title'    => $this->title,
				'status'   => $this->status,
				'push_num' => $this->push_num,
			];

			return $result;
		}

		/**
		 * @title           关注和收到消息回复
		 *
		 * @param array $data 必选 array  发送消息的参数
		 * @param author_id 必选 string  公众号授权id
		 * @param eventKey 必选 string  微信事件的key
		 * @param event 必选 string  event
		 * @param type 必选 string  1关注 2收到消息
		 * @param openid 必选 string  openid
		 * @param user_name 必选 string  user_name
		 * @param time 必选 string  时间
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 9:58
		 * @number          0
		 *
		 */
		public static function sendMessage ($data)
		{
			$author_id = $data['author_id'];
			$info      = '';
			$scene_id  = '';
			//关注和扫描时eventKey值是不一样的，关注时会带上qrscene_，扫描时则不带
			if (!empty($data['event'])) {
				if ($data['event'] == WxConstUtil::WX_SUBSCRIBE_EVENT) {
					if (!empty($data['eventKey'])) {
						$eventKeyArr = explode('_', $data['eventKey']);
						$scene_id    = $eventKeyArr[1];
					}
				} elseif ($data['event'] == WxConstUtil::WX_SCAN_EVENT) {
					$scene_id = $data['eventKey'];
				}
			}
			if (!empty($scene_id)) {
				$scene_id = intval($scene_id);
				//关注来源如果是渠道二维码，就不走关注回复了
				$scene = Scene::find()->where(['author_id' => $author_id, 'scene_id' => $scene_id, 'status' => 1])->one();
				if (!empty($scene)) {
					Yii::error($scene->id, 'interact-reply-1');

					return '';
				}
			}
			\Yii::error($data, '$data');
			//关注和收到消息回复只当时间点与多个任务重合时只按 最后一次创建的时间来算
			$result = [];
			//设置了今天或指定日期内容
			$s_time = date('Y-m-d H:i:s', $data['time']);
			/** @var InteractReply $interactDay */
			$interactDay = InteractReply::find()
				->where([
					'author_id'  => $author_id,
					'status'     => 1,
					'type'       => $data['type'],
					'reply_type' => [1, 3]
				])
				->andFilterWhere(['<=', 'start_time', $s_time])
				->andFilterWhere(['>=', 'end_time', $s_time])
				->orderBy(['create_time' => SORT_DESC])
				->one();

			//设置了每天回复内容
//			$interact_every = InteractReply::find()->andWhere(['author_id' => $author_id, 'status' => 1, 'type' => $data['type'], 'reply_type' => 2])->orderBy(['create_time' => SORT_DESC])->limit(1);
//			$interact_every = $interact_every->one();
			$everyIds = InteractReply::find()
				->alias('ir')
				->select(new Expression('max(ir.id) as id'))
				->leftJoin('{{%auto_reply}} as ar', 'ar.inter_id = ir.id')
				->where([
					'ir.author_id'  => $author_id,
					'ir.status'     => 1,
					'ir.type'       => $data['type'],
					'ir.reply_type' => 2
				])
				->groupBy('ar.time_json')
				->asArray()
				->all();
			if (!empty($everyIds)) {
				$everyIds      = array_column($everyIds, 'id');
				$interactEvery = InteractReply::find()
					->where(['id' => $everyIds])
					->orderBy(['create_time' => SORT_DESC])
					->all();
			} else {
				$interactEvery = [];
			}

			$start_time = date("Y-m-d", time());
			$end_time   = date("Y-m-d", time()) . ' 23:59:59';

			if (!empty($interactDay) && !empty($interactEvery)) {
				$dayAuto = AutoReply::findOne(['inter_id' => $interactDay->id]);

				//按指定日期的来
				if (!empty($detailDay) && $data['type'] == 2) {
					return false;
				}
				$no_send_type = $interactDay->no_send_type;
				$auto_eve     = AutoReply::find()->andWhere(['inter_id' => $interactDay->id])->asArray()->all();
				if (!empty($auto_eve)) {
					$result = static::sendData($auto_eve, $data, $no_send_type, $author_id, $interactDay);
				}

				/** @var InteractReply $interactEveryInfo */
				foreach ($interactEvery as $interactEveryInfo) {
					$detail    = InteractReplyDetail::find()->andWhere(['inter_id' => $interactEveryInfo->id, 'openid' => $data['openid']])->andFilterWhere(['between', 'create_time', $start_time, $end_time])->one();
					$detailDay = InteractReplyDetail::find()->andWhere(['inter_id' => $interactDay->id, 'openid' => $data['openid']])->andFilterWhere(['between', 'create_time', $start_time, $end_time])->one();

					if (!empty($detail) && !empty($detailDay) && $data['type'] == 2) {
						return false;
					}

					$everyAuto = AutoReply::findOne(['inter_id' => $interactEveryInfo->id]);

					$dayAutoTime   = Json::decode($dayAuto->time_json);
					$everyAutoTime = Json::decode($everyAuto->time_json);
					if ($dayAutoTime[0] != $everyAutoTime[0] || $dayAutoTime[1] != $everyAutoTime[1]) {
						//按每天的来
						if (!empty($detail) && $data['type'] == 2) {
							return false;
						}
						$no_send_type = $interactEveryInfo->no_send_type;
						$auto_eve     = AutoReply::find()->andWhere(['inter_id' => $interactEveryInfo->id])->asArray()->all();
						if (!empty($auto_eve)) {
							$result = static::sendData($auto_eve, $data, $no_send_type, $author_id, $interactEveryInfo);
						}
					}
				}
			}
			if (empty($interactDay) && !empty($interactEvery)) {
				/** @var InteractReply $interactEveryInfo */
				foreach ($interactEvery as $interactEveryInfo) {
					$detail       = InteractReplyDetail::find()->andWhere(['inter_id' => $interactEveryInfo->id, 'openid' => $data['openid']])->andFilterWhere(['between', 'create_time', $start_time, $end_time])->one();
					$no_send_type = $interactEveryInfo->no_send_type;
					$auto_eve     = AutoReply::find()->andWhere(['inter_id' => $interactEveryInfo->id])->asArray()->all();
					if (!empty($auto_eve)) {
						if ($data['type'] == 2) {
							//收到消息每个任务 每天 每个人只能收到一条
							if (!empty($detail)) {
								return false;
							}
						}
						$result = static::sendData($auto_eve, $data, $no_send_type, $author_id, $interactEveryInfo);
					}
				}
			}
			if (!empty($interactDay) && empty($interactEvery)) {
				if (strtotime($interactDay->start_time) <= $data['time'] && $data['time'] <= strtotime($interactDay->end_time)) {
					$detailDay    = InteractReplyDetail::find()->andWhere(['inter_id' => $interactDay->id, 'openid' => $data['openid']])->andFilterWhere(['between', 'create_time', $start_time, $end_time])->one();
					$no_send_type = $interactDay->no_send_type;
					$auto_eve     = AutoReply::find()->andWhere(['inter_id' => $interactDay->id])->asArray()->all();
					if (!empty($auto_eve)) {
						if ($data['type'] == 2) {
							//收到消息每个任务 每天 每个人只能收到一条
							if (!empty($detailDay)) {
								return false;
							}
						}
						$result = static::sendData($auto_eve, $data, $no_send_type, $author_id, $interactDay);
					}
				}
			}
			if (!empty($result)) {
				$news = $result['replyMsg'];
				\Yii::error($news, '$news');
				if (!empty($news)) {
					return $result;
				}
			}

		}

		/**
		 * 删除未发送的数据
		 *
		 * @param     $openid
		 * @param int $interId
		 *
		 */
		public static function removeUnPushDetail ($openid, $interId = 0)
		{
			$inter_reply_detail_id = [];
			$details               = InteractReplyDetail::find()->where(['openid' => $openid])->andWhere(['>', 'queue_id', 0]);
			if (!empty($interId)) {
				$details = $details->andWhere(['inter_id' => $interId]);
			}
			$details = $details->all();

			if (!empty($details)) {
				/** @var InteractReplyDetail $detail */
				foreach ($details as $detail) {
					\Yii::$app->queue->remove($detail->queue_id);
					array_push($inter_reply_detail_id, $detail->id);
				}
			}
			if (!empty($inter_reply_detail_id)) {
				InteractReplyDetail::deleteAll(['id' => $inter_reply_detail_id]);
			}
		}

		/**
		 * @title           封装发送数据
		 *
		 * @param array         $auto_eve       每条规则的auto_reply的对象
		 * @param array         $data           发送消息的必要参数
		 * @param int           $no_send_type   1 不推送时间为开 2为关
		 * @param int|string    $author_id      公众号授权id
		 * @param InteractReply $interact_every 每条规则的对象
		 *
		 * @return array|string[]
		 */
		public static function sendData ($auto_eve, $data, $no_send_type, $author_id, $interact_every)
		{
			static::removeUnPushDetail($data['openid'], $interact_every->id);
			$info   = [];
			$result = [];
			//今天时间戳
			$timetoday    = strtotime(date("Y-m-d", time()));
			$tomorrow     = $timetoday + 3600 * 24;
			$fromUserName = $data['user_name'];
			$time         = !empty($data['time']) ? $data['time'] : time();
			foreach ($auto_eve as $reply) {
				$time_json           = $reply['time_json'];
				$time_json           = json_decode($time_json, true);
				$detail              = [];
				$detail['author_id'] = $data['author_id'];
				$detail['inter_id']  = $interact_every->id;
				$detail['type']      = $data['type'];
				$detail['openid']    = $data['openid'];
				$detail['time']      = $data['time'];
				$detail['auto_id']   = $reply['id'];

				$reply_detail              = [];
				$reply_detail['type']      = $data['type'];
				$reply_detail['author_id'] = $author_id;
				$reply_detail['inter_id']  = $interact_every->id;
				$reply_detail['openid']    = $data['openid'];
				$reply_detail['time']      = $data['time'];
				$reply_detail['auto_id']   = $reply['id'];

				$data['inter_id'] = $interact_every->id;
				if (empty(intval($time_json[0])) && empty(intval($time_json[1]))) {
					if ($no_send_type == 1) {
						//不推送时间段为开
						$no_send_time = json_decode($interact_every->no_send_time);
						$date1        = date("Y-m-d", time());
						$stime1       = $date1 . ' ' . $no_send_time[0];
						$stime2       = $date1 . ' ' . $no_send_time[1];
						if (time() >= strtotime($stime1) && time() <= strtotime($stime2)) {
							$second = strtotime($stime2) - time() + 60;
							//返回推送明细的id
							$inter_id = InteractReplyDetail::create($reply_detail, 3);
							\Yii::error($inter_id, '$inter_id');
							$jobId = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
								'author_id'      => $author_id,
								'openid'         => $data['openid'],
								'auto_id'        => $reply['id'],
								'type'           => $data['type'],
								'inter_id'       => $inter_id,
								'inter_reply_id' => $interact_every->id,
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
							$result = static::returnMsg($reply, $author_id, $data, $detail, $fromUserName, $time, $interact_every);
						}
					} else {
						//不推送时间段为关
						$result = static::returnMsg($reply, $author_id, $data, $detail, $fromUserName, $time, $interact_every);
					}

				} else {
					if (intval($time_json[0]) > 0 && intval($time_json[1]) == 0) {
						$time = intval($time_json[0]) * 3600;
					} elseif (intval($time_json[0]) == 0 && intval($time_json[1]) > 0) {
						$time = $time_json[1] * 60;
					} elseif (intval($time_json[0]) > 0 && intval($time_json[1]) > 0) {
						$time = intval($time_json[0]) * 3600 + intval($time_json[1]) * 60;
					}
					//非立即发送
					if ($no_send_type == 1) {
						//不推送时间段为开
						$no_send_time = json_decode($interact_every->no_send_time);
						$stime        = time() + $time;
						$date1        = date("Y-m-d", $stime);
						$stime1       = $date1 . ' ' . $no_send_time[0];
						$stime2       = $date1 . ' ' . $no_send_time[1];
						//当前时间处于安静模式下
						if (time() + $time >= strtotime($stime1) && time() + $time <= strtotime($stime2)) {
							\Yii::error($stime2, '$stime2');
							\Yii::error($time, '$time');
							//0点到7点  5点关注 1小时后推送  3小时后推送
							$second = strtotime($stime2) - time() + 60;
							\Yii::error($second, '$second');
							$inter_id = InteractReplyDetail::create($reply_detail, 3);
							\Yii::error($inter_id, '$inter_id');
							$jobId = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
								'author_id'      => $author_id,
								'openid'         => $data['openid'],
								'auto_id'        => $reply['id'],
								'type'           => $data['type'],
								'inter_id'       => $inter_id,
								'inter_reply_id' => $interact_every->id,
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
							$second = $time;
							\Yii::error($second, '$second');
							//返回推送明细的id
							$inter_id = InteractReplyDetail::create($reply_detail, 3);
							\Yii::error($inter_id, '$inter_id');
							\Yii::error($interact_every->id, '$interact_every_id');
							$jobId = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
								'author_id'      => $author_id,
								'openid'         => $data['openid'],
								'auto_id'        => $reply['id'],
								'type'           => $data['type'],
								'inter_id'       => $inter_id,
								'inter_reply_id' => $interact_every->id,
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
						$second = $time;
						//返回推送明细的id
						$inter_id = InteractReplyDetail::create($reply_detail, 3);
						\Yii::error($inter_id, '$inter_id');
						$jobId = \Yii::$app->queue->delay($second)->push(new InterReplyJob([
							'author_id'      => $author_id,
							'openid'         => $data['openid'],
							'auto_id'        => $reply['id'],
							'type'           => $data['type'],
							'inter_id'       => $inter_id,
							'inter_reply_id' => $interact_every->id,
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
			if (!empty($result)) {
				return $result;
			}
		}

		//回复消息
		private function returnMsg ($reply, $author_id, $data, $detail, $fromUserName, $time, $interact_every)
		{
			\Yii::error($interact_every->id, '$interact_every->id');
			$source = 1;
			if ($data['type'] == 2) {
				$source = 3;
			}
			$auto            = AutoReply::findOne(['id' => $reply['id']]);
			$data['auto_id'] = $reply['id'];
			if (!empty($auto)) {
				$wxAuthorize = WxAuthorize::getTokenInfo($auto->author->authorizer_appid, false, true);
			}
			$xmlData   = '';
			$replyList = InteractReply::getReplyList($author_id, $data['openid'], $auto);
			if (!empty($replyList)) {
				$toUserName   = $data['openid'];
				$wechat       = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $auto->author->authorizer_appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);
				$fans         = Fans::findOne(['openid' => $data['openid'], 'author_id' => $author_id]);
				$articleCount = 0;
				$error_code   = 0;
				$error_msg    = '';
				$flag         = 0;
				$articlesMsg  = [];
				foreach ($replyList as $rv) {
					if ($rv['type'] == 1) {
						$result = $wechat->sendText($toUserName, $rv['content']);
						if ($result['errcode'] == 0) {
							$flag = 1;
							FansTimeLine::create($fans->id, 'text', time(), 0, $source, $auto->time_json);
						}
					} elseif ($rv['type'] == 2) {
						$result = $wechat->sendImage($toUserName, $rv['content']);
						\Yii::error($data['openid'], 'openid');
						\Yii::error($result, 'result-image');
						if ($result['errcode'] == 0) {
							$flag = 1;
							FansTimeLine::create($fans->id, 'image', time(), 0, $source, $auto->time_json);
						}
					} elseif ($rv['type'] == 3) {
						$result = $wechat->sendVoice($toUserName, $rv['content']);
						if ($result['errcode'] == 0) {
							$flag = 1;
							FansTimeLine::create($fans->id, 'voice', time(), 0, $source, $auto->time_json);
						}
					} elseif ($rv['type'] == 4) {
						$title      = $rv['title'];
						$attachment = Attachment::findOne($rv['material_id']);
						if (!empty($attachment->file_name)) {
							$title = $attachment->file_name;
						}
						$result = $wechat->sendVideo($toUserName, $rv['content'], '', $title, $rv['digest']);
						if ($result['errcode'] == 0) {
							$flag = 1;
							FansTimeLine::create($fans->id, 'video', time(), 0, $source, $auto->time_json);
						}
					} elseif ($rv['type'] == 5) {
						//图文走被动回复
						$articles = '';
						foreach ($rv['content'] as $rvv) {
							if ($articleCount > 8) {
								continue;
							}
							if (!empty($rvv['material_id'])) {
								$attachment = Attachment::findOne($rvv['material_id']);
								if ($attachment->status == 0) {
									$error_code = '99999';
									$error_msg  = '素材已被删除，请重新创建';
									continue;
								}
							}
							$articles
								.= "<item>
										      <Title><![CDATA[" . $rvv['title'] . "]]></Title>
										      <Description><![CDATA[" . $rvv['digest'] . "]]></Description>
										      <PicUrl><![CDATA[" . $rvv['pic_url'] . "]]></PicUrl>
										      <Url><![CDATA[" . $rvv['content_url'] . "]]></Url>
										    </item>";
							$articleCount++;

							$articles1 = [
								"title"       => $rvv['title'],
								"description" => $rvv['digest'],
								"url"         => $rvv['content_url'],
								"picurl"      => $rvv['pic_url']
							];
							array_push($articlesMsg, $articles1);
						}
						//只有关注回复走被动回复
						if (!empty($articles) && $data['type'] == 1) {
							$xmlData
								= "<xml>
									  <ToUserName><![CDATA[" . $toUserName . "]]></ToUserName>
									  <FromUserName><![CDATA[" . $fromUserName . "]]></FromUserName>
									  <CreateTime>" . $time . "</CreateTime>
									  <MsgType><![CDATA[news]]></MsgType>
									  <ArticleCount>" . $articleCount . "</ArticleCount>
									  <Articles>" . $articles . "</Articles>
									</xml>";
						}

					}
				}
				if ($articleCount <= 1 || ($data['type'] == 2 && $articleCount > 1)) {
					if (!empty($articlesMsg)) {
						foreach ($articlesMsg as $msg) {
							$arr[]  = $msg;
							$result = $wechat->sendNews($toUserName, $arr);
							if ($result['errcode'] == 0) {
								$flag = 1;
								FansTimeLine::create($fans->id, 'news', time(), 0, $source, $auto->time_json);
							}
							unset($arr);
						}
					}
					if (!empty($result) && $result['errcode'] != 0) {
						$error_code = $result['errcode'];
						$error_msg  = $result['errmsg'];
					}
					if ($flag == 1) {
						$error_code = 0;
						$error_msg  = '';
					}
					$data['error_code'] = $error_code;
					$data['error_msg']  = $error_msg;
					if (!empty($error_code)) {
						InteractReplyDetail::create($data, 2);
					} else {
						InteractReplyDetail::create($data, 1);
					}
					//插入统计数据
					InteractReply::insertStatisc($interact_every->id, $author_id);
				}
				if (!empty($xmlData) && $articleCount > 1) {
					return ['replyMsg' => $xmlData, 'detail' => $detail, 'time' => $data['time'], 'inter_id' => $interact_every->id, 'author_id' => $author_id, 'time_json' => $reply['time_json']];
				} else {
					return ['replyMsg' => ''];
				}

			}
		}

		/**
		 * @title           获取回复内容数据
		 *
		 * @param $author_id  公众号授权id
		 * @param $openid     openid
		 * @param $auto       auto
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:25
		 * @number          0
		 *
		 */
		public static function getReplyList ($author_id, $openid, $auto)
		{
			try {
				$fans      = Fans::find()->where(['author_id' => $author_id, 'openid' => $openid])->one();
				$host      = \Yii::$app->params['site_url'];
				$pattenArr = [
					'nickname' => $fans->nickname
				];
				$replyInfo = ReplyInfo::find()->andWhere(['rp_id' => $auto->id])->asArray()->all();
				$replyList = [];
				$site_url  = \Yii::$app->params['site_url'];
				foreach ($replyInfo as $rv) {
					if ($rv['type'] == 5) {
						if (!isset($tempId)) {
							$tempId                     = $rv['id'];
							$replyList[$tempId]['type'] = 5;
						}
						$from = 0;//1来自导入
						if (empty($rv['is_use']) && !empty($rv['attachment_id'])) {
							$attachment = Attachment::findOne($rv['attachment_id']);
							if (empty($attachment->status)) {
								break;
							} else {
								$from = 1;
							}
						}
						if (!empty($rv['material_id']) && empty($rv['title'])) {
							$material = Material::findOne(['id' => $rv['material_id']]);
							$article  = Article::find()->alias('a');
							$article  = $article->leftJoin('{{%material}} m', 'm.id = a.thumb_media_id');
							$article  = $article->where(['a.id' => $material->article_sort])->select('a.title,a.digest,a.content_source_url,m.local_path')->asArray()->one();
							$pic_url  = '';
							if (!empty($article['local_path'])) {
								$pic_url = $site_url . $article['local_path'];
							}
							$title              = $article['title'];
							$digest             = $article['digest'];
							$content_source_url = $article['content_source_url'];
							if ($from == 1) {
								$title              = $attachment->file_name;
								$digest             = $attachment->content;
								$content_source_url = $attachment->jump_url;
								$pic_url            = $site_url . $attachment->local_path;
							}
							$replyList[$tempId]['content'][] = ['type' => $rv['type'], 'title' => $title, 'material_id' => $rv['attachment_id'], 'digest' => $digest, 'content_url' => $content_source_url, 'pic_url' => $pic_url];
						} else {
							$title   = Scene::pregReplaceCallback($rv['title'], $pattenArr);
							$pic_url = '';
							if (!empty($rv['cover_url'])) {
								$pic_url = $site_url . $rv['cover_url'];
							}
							$digest      = $rv['digest'];
							$content_url = $rv['content_url'];
							if ($from == 1) {
								$title       = $attachment->file_name;
								$digest      = $attachment->content;
								$content_url = $attachment->jump_url;
								$pic_url     = $site_url . $attachment->local_path;
							}
							$replyList[$tempId]['content'][] = ['type' => $rv['type'], 'title' => $title, 'digest' => $digest, 'material_id' => $rv['attachment_id'], 'content_url' => $content_url, 'pic_url' => $pic_url];
						}
					} elseif ($rv['type'] == 1) {
						$content              = Scene::pregReplaceCallback(rawurldecode($rv['content']), $pattenArr);
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $content];
					} else {
						$material = Material::findOne(['id' => $rv['material_id']]);
						if (in_array($rv['type'], [2, 3, 4])) {
							MsgUtil::checkNeedReload($material);
						}
						$title   = !empty($material->file_name) ? $material->file_name : '';
						$digest  = '';//先不加
						$pic_url = '';
						if (!empty($material->local_path)) {
							$pic_url = $site_url . $material->local_path;
						}
						$replyList[$rv['id']] = ['type' => $rv['type'], 'content' => $material->media_id, 'material_id' => $rv['attachment_id'], 'pic_url' => $pic_url, 'title' => $title, 'digest' => $digest];
					}
				}

				return $replyList;
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'getReplyList_message');
			}

		}

		/**
		 * @title           插入统计数据
		 *
		 * @param $inter_id     规则的id
		 * @param $author_id    公众号授权id
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:25
		 * @number          0
		 *
		 */
		public static function insertStatisc ($inter_id, $author_id)
		{
			try {
				$date1     = date("Y-m-d", time());
				$date2     = date("Y-m-d", strtotime("+1 day"));
				$inter_sta = InteractStatistic::find()->where(['inter_id' => $inter_id, 'date_time' => $date1])->one();
				$wxinfo    = WxAuthorizeInfo::find()->where(['author_id' => $author_id])->one();
				$count     = InteractReplyDetail::find()->where(['inter_id' => $inter_id, 'status' => 0])->andFilterWhere(['between', 'create_time', $date1, $date2])->groupBy("openid")->count();
				if (empty($inter_sta)) {
					$inter_sta              = new InteractStatistic();
					$inter_sta->name        = $wxinfo->nick_name;
					$inter_sta->inter_id    = $inter_id;
					$inter_sta->send_num    = 1;
					$inter_sta->receive_num = $count;
					$inter_sta->date_time   = $date1;

				} else {
					$send_num               = $inter_sta->send_num;
					$send_num               += 1;
					$inter_sta->send_num    = $send_num;
					$inter_sta->receive_num = $count;
				}
				if (!$inter_sta->validate() || !$inter_sta->save()) {
					\Yii::error(SUtils::modelError($inter_sta), 'modelError');
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'insertStatisc_message');
			}

		}

		/**
		 * @title           根据日期获取具体的每一天
		 *
		 * @param $startdate 必选 string 开始日期
		 * @param $enddate   必选 string 结束日期
		 *
		 * @return          {"error":0,"data":[]}
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:06
		 * @number          0
		 *
		 */
		public static function getDate ($startdate, $enddate)
		{
			$stimestamp = strtotime($startdate);
			$etimestamp = strtotime($enddate);

			// 计算日期段内有多少天
			$days = ($etimestamp - $stimestamp) / 86400 + 1;

			// 保存每天日期
			$date = [];

			for ($i = 0; $i < $days; $i++) {
				$date[] = date('Y-m-d', $stimestamp + (86400 * $i));
			}

			return $date;
		}

		//根据时间封装默认数据 $data 日期的二维数组

		/**
		 * @title           根据时间封装默认数据
		 *
		 * @param $date 必选 array 日期
		 * @param $name 必选 string 公众号名称
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:08
		 * @number          0
		 *
		 */
		public static function getDateData ($date, $name)
		{
			$result = [];
			foreach ($date as $k => $v) {
				$result[$k]['name']        = $name;
				$result[$k]['send_num']    = 0;
				$result[$k]['receive_num'] = 0;
				$result[$k]['date_time']   = $v;
			}

			return $result;
		}

		/**
		 * @title           默认数据与查询的数据整合得到新的数组
		 *
		 * @param $data1 必选 array 默认数据
		 * @param $data2 必选 array 数据查询的数据
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:09
		 * @number          0
		 *
		 */
		public static function getNewData ($data1, $data2, $nick_name)
		{
			$result = [];
			foreach ($data1 as $k => $v) {
				$result[$k]['name']        = $nick_name;
				$result[$k]['send_num']    = 0;
				$result[$k]['date_time']   = $v;
				$result[$k]['receive_num'] = 0;
				foreach ($data2 as $kk => $vv) {
					if ($vv['date_time'] == $v) {
						$result[$k]['name']        = $vv['name'];
						$result[$k]['send_num']    = $vv['send_num'];
						$result[$k]['date_time']   = $vv['date_time'];
						$result[$k]['receive_num'] = $vv['receive_num'];
					}
				}
			}

			return $result;
		}

		/**
		 * @title           封装数据
		 *
		 * @param     $date1     开始日期
		 * @param     $date2     结束日期
		 * @param     $page      当前页
		 * @param     $days      总天数
		 * @param     $pageSize  页数
		 * @param     $interData 查询规则的对象
		 * @param     $nick_name 公众号名称
		 * @param     $id        所查询规则id
		 * @param int $type      1查询所有
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:17
		 * @number          0
		 *
		 */
		public static function getStatisticData ($date1, $date2, $page, $days, $pageSize, $interData, $nick_name, $id, $type = 0)
		{
			$result = [];
			$count  = $days;
			if ($type == 1) {
				//全部数据
				$stime = strtotime($interData->create_time);
				$etime = strtotime($interData->create_time) + ($count - 1) * 86400;
				$date1 = date("Y-m-d H:i:s", $stime);
				$date2 = date("Y-m-d", $etime);
			} else {
				if ($days > $pageSize) {
					$offset = $page * $pageSize;
					if ($offset < $count) {
						$stime = strtotime($interData->create_time) + ($page - 1) * $pageSize * 86400;
						$etime = strtotime($interData->create_time) + $page * $pageSize * 86400;
					} else {
						$stime = strtotime($interData->create_time) + ($page - 1) * $pageSize * 86400;
						$etime = strtotime($interData->create_time) + $count * 86400;
					}
					$date1 = date("Y-m-d H:i:s", $stime);
					$date2 = date("Y-m-d", $etime);
				}
			}
			$date_new1 = explode(' ', $date1);
			$date_new2 = explode(' ', $date2);
			$date1     = $date_new1[0];
			$date2     = $date_new2[0];
			$sta       = InteractStatistic::find()->where(['inter_id' => $id])->andFilterWhere(['between', 'date_time', $date1, $date2]);
			$sta       = $sta->asArray()->all();
			$data      = [];
			//组装默认数据
			$dateData = static::getDate($date1, $date2);
			if (!empty($sta)) {
				foreach ($sta as $key => $v) {
					$data[$key]['name']        = $v['name'];
					$data[$key]['send_num']    = $v['send_num'];
					$data[$key]['receive_num'] = $v['receive_num'];
					$data[$key]['date_time']   = $v['date_time'];
				}
				$result = static::getNewData($dateData, $data, $nick_name);
			} else {
				$result = static::getDateData($dateData, $nick_name);
			}

			return $result;
		}

		/**
		 * @title           获取最终的统计数据
		 *
		 * @param     $count      数据的总数 默认0
		 * @param     $result     默认数组空
		 * @param     $interData  查询规则的对象
		 * @param     $nick_name  公众号名称
		 * @param     $id         所查询规则id
		 * @param     $page       当前页
		 * @param     $pageSize   页数
		 * @param     $type       默认0 1查询所有
		 *
		 * @return          {"error":0,"data":[]}
		 *
		 * @return_param    error int 状态码
		 * @return_param    data array 结果数据
		 *
		 * @remark          Create by PhpStorm. User: wangpan. Date: 2019/11/22 10:11
		 * @number          0
		 *
		 */
		public static function getLastData ($count, $result, $interData, $nick_name, $id, $page = 1, $pageSize = 15, $type = 0)
		{
			if ($interData->reply_type == 1) {
				$info = [];
				//今天
				$count             = 1;
				$time              = explode(' ', $interData->create_time);
				$info['date_time'] = $time[0];
				$info['name']      = $nick_name;
				$date1             = $interData->create_time;
				$date1             = explode(' ', $date1);
				$create_time       = strtotime($interData->create_time);
				$date2             = date("Y-m-d", strtotime("+1 day"));
				$sta               = InteractStatistic::find()->where(['inter_id' => $id])->andFilterWhere(['between', 'date_time', $date1[0], $date2])->one();
				if (!empty($sta)) {
					$info['send_num']    = $sta->send_num;
					$info['receive_num'] = $sta->receive_num;
				} else {
					$info['send_num']    = 0;
					$info['receive_num'] = 0;
				}
				$result[] = $info;
			} elseif ($interData->reply_type == 2) {
				//每天
				$date1 = $interData->create_time;
				if ($interData->status == 1) {
					//开启
					$date2 = DateUtil::getCurrentTime();
				} else {
					//关闭
					$date2 = $interData->close_time;
				}
				$date1  = explode(' ', $date1);
				$date2  = explode(' ', $date2);
				$days   = ceil((strtotime($date2[0] . ' 23:59:59') - strtotime($date1[0])) / 86400);
				$count  = $days;
				$result = static::getStatisticData($date1[0], $date2[0] . ' 23:59:59', $page, intval($days), $pageSize, $interData, $nick_name, $id, $type);

			} elseif ($interData->reply_type == 3) {
				//指定日期
				$current_time = DateUtil::getCurrentTime();
				if (strtotime($current_time) < strtotime($interData->start_time)) {
					return [
						'count'  => $count,
						'result' => $result,
					];
				}
				if ($interData->status == 1) {
					//开启
					if (strtotime($current_time) > strtotime($interData->end_time)) {
						$date1 = $interData->start_time;
						$date2 = $interData->end_time;
					} else {
						$date1 = $interData->start_time;
						$date2 = $current_time;
					}
				} else {
					//关闭
					if (strtotime($interData->close_time) > strtotime($interData->end_time)) {
						$date1 = $interData->start_time;
						$date2 = $interData->end_time;
					} else {
						$date1 = $interData->start_time;
						$date2 = $interData->close_time;
					}
				}
				$date1  = explode(' ', $date1);
				$date2  = explode(' ', $date2);
				$days   = ceil((strtotime($date2[0] . ' 23:59:59') - strtotime($date1[0])) / 86400);
				$count  = $days;
				$result = static::getStatisticData($date1[0], $date2[0] . ' 23:59:59', $page, $days, $pageSize, $interData, $nick_name, $id, $type);

			}
			$info['count']  = $count;
			$info['result'] = $result;

			return $info;
		}

	}
