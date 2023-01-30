<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2019/10/14
	 * Time: 16:43
	 */

	namespace app\queue;

	use app\models\FansTimeLine;
	use app\models\TemplatePushInfo;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;
	use app\models\TemplatePushMsg;
	use app\models\Template;
	use app\models\WxAuthorize;
	use app\models\Fans;
	use callmez\wechat\sdk\Wechat;

	class TemplateJob extends BaseObject implements JobInterface
	{
		public $template_push_msg_id;
		public $appid;
		public $limit = 5000;
		public $offset = 0;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			$template_push_msg_id = $this->template_push_msg_id;
			$appid                = $this->appid;

			$tmpMsg      = TemplatePushMsg::findOne($template_push_msg_id);
			$wxAuthorize = WxAuthorize::getTokenInfo($appid, false, true);
			if (!empty($wxAuthorize)) {
				$wechat = \Yii::createObject([
					'class'          => Wechat::className(),
					'appId'          => $appid,
					'appSecret'      => $wxAuthorize['config']->appSecret,
					'token'          => $wxAuthorize['config']->token,
					'componentAppId' => $wxAuthorize['config']->appid,
				]);
			}
			$msgId       = [];
			$error_code  = 0;
			$error_msg   = '';
			$temp        = Template::findOne(['id' => $tmpMsg->template_id]);
			$template_id = $temp->template_id;

			$push_rule = json_decode(base64_decode($tmpMsg->push_rule), true);
			$sex       = $push_rule['sex'];
			$stime     = $push_rule['stime'];
			$etime     = $push_rule['etime'];
			$province  = $push_rule['province'];
			$city      = $push_rule['city'];
			$tag_ids   = $push_rule['tag_ids'];
			$send_type = $push_rule['send_type'];

			if ($send_type == 2 && strtotime($tmpMsg->push_time) > time()) {
				return false;
			}

			$content     = $temp->content;
			$content     = ltrim($content, '{{first.DATA}}');
			$content     = rtrim($content, '{{remark.DATA}}');
			$con         = explode(PHP_EOL, $content);
			$miniprogram = [];
			if ($tmpMsg->redirect_type == 1) {
				$url = $tmpMsg->url;
			} elseif ($tmpMsg->redirect_type == 2) {
				$url                     = '';
				$miniprogram['appid']    = $tmpMsg->appid;
				$miniprogram['pagepath'] = $tmpMsg->pagepath;
			}
			$fans_num = 0;
			//获取发送粉丝
			$push_type = $tmpMsg->push_type;
			if ($push_type == 1) {
				$fans = Fans::find()->andWhere(['f.author_id' => $tmpMsg->author_id, 'f.subscribe' => 1])->alias('f');
				$fans = $fans->select('f.openid,f.nickname,f.id');
				if (!empty($sex)) {
					if ($sex == 3) {
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
					$fans   = $fans->leftJoin('{{%fans_tags}} ft', '`ft`.`fans_id` = `f`.`id`')->andWhere(['and', ['f.author_id' => $tmpMsg->author_id], ['in', 'ft.tags_id', $tagIds]])->groupBy('f.id');
				}
				$count = $fans->count();
				if (empty($count) && empty($this->offset)) {
					$tmpMsg->status     = 2;
					$tmpMsg->queue_id   = 0;
					$tmpMsg->error_code = $error_code;
					$tmpMsg->error_msg  = '当前没有可发送的粉丝';
					$tmpMsg->save();

					return false;
				}
				$fans   = $fans->limit($this->limit)->offset($this->offset)->asArray()->all();
				$jobIds = [];
				$nowCount = 0;
				foreach ($fans as $k => $v) {
					$nowCount++;
					try {
						//模板内容包含昵称
						$nickname = $v['nickname'];
						//是否包含昵称
						$template_data = json_decode($tmpMsg->template_data, true);
						$template_data = Template::replaceTemplateData($template_data, $nickname, $temp, $con);
						$infoId        = TemplatePushInfo::create($tmpMsg->id, $v);

						$jobId = \Yii::$app->template->push(new TemplateSendJob([
							'appid'             => $appid,
							'fans'              => $v,
							'templateId'        => $template_id,
							'templateData'      => $template_data,
							'url'               => $url,
							'miniprogram'       => $miniprogram,
							'templatePushMsgId' => $tmpMsg->id,
							'title'             => $temp->title,
							'pushInfoId'        => $infoId,
						]));

						TemplatePushInfo::updateAll(['queue_id' => $jobId], ['id' => $infoId]);

						array_push($jobIds, $jobId);

//						$result        = $wechat->sendTemplateMessage($v['openid'], $template_id, $template_data, $url, $miniprogram, $tmpMsg->redirect_type);
//						\Yii::error($result, 'result-0');
//						if ($result['errcode'] == 0) {
//							//插入粉丝轨迹
//							FansTimeLine::create($v['id'], 'template', time(), 0, 0, $temp->title);
//							array_push($msgId, $result['msgid']);
//						} else {
//							$error_code = $result['errcode'];
//							$error_msg  = $result['errmsg'];
//						}
					} catch (\Exception $e) {
						\Yii::$app->queue->push(new LogJob([
							'data' => [
								'from'  => static::className(),
								'name'  => 'getMessage-rule',
								'index' => $k,
								'fans'  => $v,
								'data'  => $e->getMessage()
							]
						]));
					}
				}

				$hasEnd = false;
				do {
					foreach ($jobIds as $jobId) {
						$complete = true;
						if (!\Yii::$app->queue->isDone($jobId)) {
							$complete = false;
						}

						if ($complete) {
							$hasEnd = true;

							$jobId = \Yii::$app->template->push(new TemplateSendJob([
								'end'               => true,
								'templatePushMsgId' => $tmpMsg->id,
							]));
						}
					}
				} while (!$hasEnd);

				if($nowCount >= $this->limit){
					//再次插入队列
					\Yii::$app->template->push(new TemplateJob([
						'template_push_msg_id' => $this->template_push_msg_id,
						'appid'                => $this->appid,
						'offset'               => $this->offset + $this->limit
					]));
				}

				return false;

			} elseif ($push_type == 2) {
				//全部粉丝
				$fans  = Fans::find()->andWhere(['author_id' => $tmpMsg->author_id, 'subscribe' => 1])->select('openid,nickname,id');
				$count = $fans->count();
				if (empty($count) && empty($this->offset)) {
					$tmpMsg->status     = 2;
					$tmpMsg->queue_id   = 0;
					$tmpMsg->error_code = $error_code;
					$tmpMsg->error_msg  = '当前没有可发送的粉丝';
					$tmpMsg->save();

					return false;
				}
				$fans   = $fans->limit($this->limit)->offset($this->offset)->asArray()->all();
				$jobIds = [];
				$nowCount = 0;
				foreach ($fans as $k => $v) {
					$nowCount++;
					try {
						//模板内容包含昵称
						$nickname = $v['nickname'];
						//是否包含昵称
						$template_data = json_decode($tmpMsg->template_data, true);
						$template_data = Template::replaceTemplateData($template_data, $nickname, $temp, $con);
						$infoId        = TemplatePushInfo::create($tmpMsg->id, $v);

						$jobId = \Yii::$app->template->push(new TemplateSendJob([
							'appid'             => $appid,
							'fans'              => $v,
							'templateId'        => $template_id,
							'templateData'      => $template_data,
							'url'               => $url,
							'miniprogram'       => $miniprogram,
							'templatePushMsgId' => $tmpMsg->id,
							'title'             => $temp->title,
							'pushInfoId'        => $infoId,
						]));

						TemplatePushInfo::updateAll(['queue_id' => $jobId], ['id' => $infoId]);

						array_push($jobIds, $jobId);
					} catch (\Exception $e) {
						\Yii::$app->template->push(new LogJob([
							'data' => [
								'from'  => static::className(),
								'name'  => 'getMessage-all',
								'index' => $k,
								'fans'  => $v,
								'data'  => $e->getMessage()
							]
						]));
					}
				}

				$hasEnd = false;
				do {
					foreach ($jobIds as $jobId) {
						$complete = true;
						if (!\Yii::$app->template->isDone($jobId)) {
							$complete = false;
						}

						if ($complete) {
							$hasEnd = true;

							$jobId = \Yii::$app->template->push(new TemplateSendJob([
								'end'               => true,
								'templatePushMsgId' => $tmpMsg->id,
							]));
						}
					}
				} while (!$hasEnd);

				if($nowCount >= $this->limit){
					//再次插入队列
					\Yii::$app->template->push(new TemplateJob([
						'template_push_msg_id' => $this->template_push_msg_id,
						'appid'                => $this->appid,
						'offset'               => $this->offset + $this->limit
					]));
				}

				return false;
			} elseif ($push_type == 3) {
				//指定粉丝
				$openidArr = explode(';', $push_rule['openids']);
				\Yii::error($openidArr, '$openidArr-0');
				if (empty(count($openidArr))) {
					$tmpMsg->status     = 2;
					$tmpMsg->queue_id   = 0;
					$tmpMsg->error_code = $error_code;
					$tmpMsg->error_msg  = '输入的指定粉丝有误';
					$tmpMsg->save();

					return false;
				}

				$fans  = Fans::find()->andWhere(['author_id' => $tmpMsg->author_id, 'subscribe' => 1,'openid'=>$openidArr])->select('openid,nickname,id');
				$count = $fans->count();
				if (empty($count)) {
					$tmpMsg->status     = 2;
					$tmpMsg->queue_id   = 0;
					$tmpMsg->error_code = $error_code;
					$tmpMsg->error_msg  = '当前没有可发送的粉丝';
					$tmpMsg->save();

					return false;
				}
				$fans   = $fans->asArray()->all();
				$jobIds = [];
				foreach ($fans as $k => $v) {
					try {
						//模板内容包含昵称
						$nickname = $v['nickname'];
						//是否包含昵称
						$template_data = json_decode($tmpMsg->template_data, true);
						$template_data = Template::replaceTemplateData($template_data, $nickname, $temp, $con);
						$infoId        = TemplatePushInfo::create($tmpMsg->id, $v);

						$jobId = \Yii::$app->template->push(new TemplateSendJob([
							'appid'             => $appid,
							'fans'              => $v,
							'templateId'        => $template_id,
							'templateData'      => $template_data,
							'url'               => $url,
							'miniprogram'       => $miniprogram,
							'templatePushMsgId' => $tmpMsg->id,
							'title'             => $temp->title,
							'pushInfoId'        => $infoId,
						]));

						TemplatePushInfo::updateAll(['queue_id' => $jobId], ['id' => $infoId]);

						array_push($jobIds, $jobId);
					} catch (\Exception $e) {
						\Yii::$app->template->push(new LogJob([
							'data' => [
								'from'  => static::className(),
								'name'  => 'getMessage-all',
								'index' => $k,
								'fans'  => $v,
								'data'  => $e->getMessage()
							]
						]));
					}
				}
				$hasEnd = false;
				do {
					foreach ($jobIds as $jobId) {
						$complete = true;
						if (!\Yii::$app->template->isDone($jobId)) {
							$complete = false;
						}

						if ($complete) {
							$hasEnd = true;

							$jobId = \Yii::$app->template->push(new TemplateSendJob([
								'end'               => true,
								'templatePushMsgId' => $tmpMsg->id,
							]));
						}
					}
				} while (!$hasEnd);

				return false;
//				if (!empty($openidArr)) {
//					foreach ($openidArr as $v) {
//						$fans = Fans::find()->andWhere(['author_id' => $tmpMsg->author_id, 'openid' => $v])->select('nickname,id');
//						$fans = $fans->one();
//						if (!empty($fans)) {
//							try {
//								//模板内容包含昵称
//								$nickname = $fans->nickname;
//								//是否包含昵称
//								$template_data = json_decode($tmpMsg->template_data, true);
//								$template_data = Template::replaceTemplateData($template_data, $nickname, $temp, $con);
//								$result        = $wechat->sendTemplateMessage($v, $template_id, $template_data, $url, $miniprogram, $tmpMsg->redirect_type);
//								\Yii::error($result, 'result-2');
//								if ($result['errcode'] == 0) {
//									//插入粉丝轨迹
//									FansTimeLine::create($fans->id, 'template', time(), 0, 0, $temp->title);
//									array_push($msgId, $result['msgid']);
//								} else {
//									$error_code = $result['errcode'];
//									$error_msg  = $result['errmsg'];
//								}
//							} catch (\Exception $e) {
//								\Yii::error($e->getMessage(), 'getMessage');
//							}
//
//						} else {
//							$error_msg = '粉丝不存在，可能因没有同步粉丝数据或是其他公众号的粉丝等原因';
//						}
//
//					}
//				}
			}

			if (empty($msgId)) {
				//发送失败
				$tmpMsg->status     = 2;
				$tmpMsg->queue_id   = 0;
				$tmpMsg->error_code = $error_code;
				$tmpMsg->error_msg  = $error_msg;
				$tmpMsg->save();
			} else {
				//更新发送成功粉丝数
				$tmpMsg->msg_id   = implode(',', $msgId);
				$tmpMsg->status   = 1;
				$tmpMsg->fans_num = count($msgId);
				$tmpMsg->queue_id = 0;
				$tmpMsg->save();
			}
		}
	}