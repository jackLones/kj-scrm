<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/14
	 * Time: 15:14
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\RedPackRule;
	use app\models\UserCorpRelation;
	use app\models\WorkChat;
	use app\models\WorkCorpAgent;
	use app\models\WorkDepartment;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkGroupSendingRedpacketSend;
	use app\models\WorkGroupSendingUser;
	use app\models\WorkTagGroupStatistic;
	use app\models\WorkTagGroupUserStatistic;
	use app\models\WorkWelcome;
	use app\util\qrstr;
	use app\util\WorkUtils;
	use app\util\DateUtil;
	use app\models\WorkCorp;
	use app\models\WorkUser;
	use app\models\WorkGroupSending;
	use app\models\ExternalTimeLine;
	use dovechen\yii2\weWork\src\dataStructure\FileMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\ImageMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\MiniprogramNoticeMessageContent;
	use dovechen\yii2\weWork\src\dataStructure\NewsMessageContent;
	use dovechen\yii2\weWork\src\dataStructure\VideoMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\VoiceMesssageContent;
	use dovechen\yii2\weWork\src\dataStructure\ExternalContactMsgTemplate;
	use yii\base\BaseObject;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use yii\queue\JobInterface;

	class WorkGroupSendingJob extends BaseObject implements JobInterface
	{

		public $work_group_sending_id;

		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);
			$sending = WorkGroupSending::findOne($this->work_group_sending_id);
			\Yii::error($this->work_group_sending_id, 'work_group_sending_id');
			try {
				$authCorp = WorkCorp::findOne($sending->corp_id);
				if ($sending->send_type == 1 || $sending->send_type == 2) {
					//客户
					$result = WorkGroupSending::sendExternalData($sending->id);
					\Yii::error($result,'sym-$result');
					$external_ids = $result['result'];
//					$workUserId = $result['userId'];
					$data         = $sending->dumpData();
					$intervalNum  = $sending->interval_num; //间隔人数
					$interval     = $sending->interval; //是否开启间隔 1 关 2 开
					//设置了时间间隔
					if ($sending->interval == 2) {
						$intervalTime = $sending->interval_time; //间隔时间
						$delay        = 0;
						switch ($intervalTime) {
							case 1:
								//$delay = 30;
								$delay = 1800;
								break;
							case 2:
								$delay = 3600;
								break;
							case 3:
								$delay = 7200;
								break;
							case 4:
								$delay = 10800;
								break;
							case 5:
								$delay = 14400;
								break;
						}
						$sendUser  = WorkGroupSendingUser::findOne(['send_id' => $this->work_group_sending_id, 'status' => 0]);
						$sendUser1 = WorkGroupSendingUser::findOne(['send_id' => $this->work_group_sending_id]);
						if (!empty($sendUser)) {
							$jobId             = \Yii::$app->queue->delay($delay)->push(new WorkGroupSendingJob([
								'work_group_sending_id' => $sending->id
							]));
							$sending->queue_id = $jobId;
							$sending->save();
						}
						if (empty($sendUser1)) {
							$jobId             = \Yii::$app->queue->delay($delay)->push(new WorkGroupSendingJob([
								'work_group_sending_id' => $sending->id
							]));
							$sending->queue_id = $jobId;
							$sending->save();
						}

					}

					$data['external_userid'] = $external_ids;

					if (empty($external_ids)) {
						$sending->status    = 2;
						$sending->error_msg = '当前发送客户为空';
						$sending->save();

						return false;
					}

					$sendId  = [];
					$uids    = [];
					$others  = !empty($sending->others) ? json_decode($sending->others, true) : [];
					$userIds = isset($others['user_ids']) && !empty($others['user_ids']) ? $others['user_ids'] : [];
					if (!empty($userIds)) {
						$user_ids  = array_column($userIds,"id");
						$workUser  = WorkUser::find()->where(["in", "id", $user_ids])->andWhere(["corp_id" => $sending->corp_id, "status" => 1, "is_del" => WorkUser::USER_NO_DEL])->andWhere("userid is not null")->select("id,userid")->asArray()->all();
						$userid    = array_column($workUser, "userid", "id");
						$userKey   = array_keys($userid);
						$userValue = array_values($userid);
						array_push($sendId, ...$userValue);
						array_push($uids, ...$userKey);

//						foreach ($userIds as $user) {
//							$workUser = WorkUser::findOne($user['id']);
//							if (!empty($workUser) && $workUser->status == 1 && $workUser->is_del==WorkUser::USER_NO_DEL) {
//								array_push($sendId, $workUser->userid);
//								array_push($uids, $workUser->id);
//							}
//						}
					}

					if (empty($sendId)) {
						$sending->status    = 2;
						$sending->error_msg = '当前发送客户的员工状态不是已激活状态';
						$sending->save();

						return false;
					}
					$userId               = [];
					$workExternalUserData = $result['workExternalUserDataNew'];
					if (!empty($uids)) {
//						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
//						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
//						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $sending->corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
//						$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $workUserId])->andWhere(['we.external_userid' => $external_ids])->select('we.external_userid wid,wf.user_id')->groupBy('we.id');
//						\Yii::error($workExternalUserData->createCommand()->getRawSql(), 'sql');
//						$workExternalUserData = $workExternalUserData->asArray()->all();
						$arr                  = array_column($workExternalUserData, 'user_id');
						$arr                  = array_count_values($arr);
						if (!empty($workExternalUserData)) {
							foreach ($workExternalUserData as $v) {
								array_push($userId, $v['user_id']);
							}
							$userId = array_unique($userId);
						}

						foreach ($uids as $uid) {
							if (in_array($uid, $userId)) {
								$num = $arr[$uid];
								if ($interval == 1) {
									WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id);
								} else {
									$tagUserStatistic = WorkTagGroupUserStatistic::findOne(['user_id' => $uid, 'send_id' => $this->work_group_sending_id]);
									//开启时间间隔
									if (ceil($num / $intervalNum) > 1) {
										WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id, 1);
										if (empty($tagUserStatistic)) {
											$count = ceil($num / $intervalNum);
											for ($i = 1; $i <= $count; $i++) {
												WorkGroupSendingUser::add($this->work_group_sending_id, $uid, $i);
											}
										}
									} else {
										if (empty($tagUserStatistic)) {
											WorkGroupSendingUser::add($this->work_group_sending_id, $uid, 1);
										}
										WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id);
									}
								}

							} else {
								WorkTagGroupUserStatistic::add('', $uid, 0, $sending->id);
							}

//							$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
//							$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
//							$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $sending->corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
//							$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $uid])->select('we.external_userid wid');
//							$workExternalUserData = $workExternalUserData->asArray()->all();
//							\Yii::error($workExternalUserData,'$workExternalUserData');
//							if (!empty($workExternalUserData)) {
//								$num = 0;
//								foreach ($workExternalUserData as $v) {
//									if (in_array($v['wid'], $external_ids)) {
//										$num++;
//									}
//								}
//								if ($interval == 1) {
//									WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id);
//								} else {
//									$tagUserStatistic = WorkTagGroupUserStatistic::findOne(['user_id' => $uid, 'send_id' => $this->work_group_sending_id]);
//									//开启时间间隔
//									if (ceil($num / $intervalNum) > 1) {
//										WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id, 1);
//										if (empty($tagUserStatistic)) {
//											$count = ceil($num / $intervalNum);
//											for ($i = 1; $i <= $count; $i++) {
//												WorkGroupSendingUser::add($this->work_group_sending_id, $uid, $i);
//											}
//										}
//									} else {
//										if (empty($tagUserStatistic)) {
//											WorkGroupSendingUser::add($this->work_group_sending_id, $uid, 1);
//										}
//										WorkTagGroupUserStatistic::add('', $uid, $num, $sending->id);
//									}
//								}
//
//							} else {
//								WorkTagGroupUserStatistic::add('', $uid, 0, $sending->id);
//							}
						}
					}

					$data['sendId'] = '';
					if (!empty($sendId)) {
						$data['sendId'] = $sendId;
					}
					$sending->save();
					$this->externalSend($authCorp, $data,$userId,$workExternalUserData);
				} elseif ($sending->send_type == 4) {
					//群发客户群
					$sendIds = WorkGroupSending::getSendData($sending->corp_id, $sending->user_key, 0);
					\Yii::error($sendIds, '$sendIds');
					$data            = $sending->dumpData();
					$data['sendIds'] = $sendIds;
					if (empty($sendIds)) {
						$sending->error_msg = '当前没有可发送的群主';
						$sending->status    = 2;
						$sending->save();

						return false;
					}
					$this->externalChat($authCorp, $data);
				} else {
					//员工
					$data           = $sending->dumpData();
					$messageContent = WorkGroupSending::sendData($data);
					\Yii::error($messageContent, '$messageContent');
					if (!empty($messageContent)) {
						$users = [];
						if (!empty($sending->user_key)) {
							$user_keys = json_decode($sending->user_key, true);
							$user_ids  = array_column($user_keys,"id");
							$workUser  = WorkUser::find()->where(["in", "id", $user_ids])->andWhere(["corp_id" => $sending->corp_id, "status" => 1, "is_del" => WorkUser::USER_NO_DEL])->andWhere("userid is not null")->select("id,userid")->asArray()->all();
							$userid    = array_column($workUser, "userid", "id");
							$userValue = array_values($userid);
							array_push($users, ...$userValue);
							\Yii::error($user_keys, '$user_keys');
							\Yii::error($user_ids, '$user_keys');

						}
						if (empty($users)) {
							$sending->error_msg = '当前发送用户为空';
							$sending->save();

							return false;
						}
						$this->messageSend($users, $sending->agentid, $messageContent, $authCorp, $data['msg_type']);
					}
				}
			} catch (\Exception $e) {
				$sending->status = 2;
				$sending->save();
				\Yii::error($e->getLine(), 'execute-sym');
				\Yii::error($e->getFile(), 'execute-sym');
				\Yii::error($e->getMessage(), 'execute-sym');
			}

		}

		/**
		 * @param $authCorp
		 * @param $data
		 * @param $uId
		 * @param $workExternalUserData
		 *
		 * @throws \Throwable
		 */
		private function externalSend ($authCorp, $data,$uId,$workExternalUserData)
		{
			try {
				$sending         = WorkGroupSending::findOne($this->work_group_sending_id);
				$interval        = $sending->interval;
				$workApi         = WorkUtils::getWorkApi($authCorp->id, WorkUtils::EXTERNAL_API);
				$external_userid = $data['external_userid'];

				if ($sending->is_redpacket == 0){
					$content          = $data['content'];
					$attachment_id    = $data['attachment_id'];
					$work_material_id = $data['work_material_id'];
					$sendData         = json_decode($content, true);
					$sendData         = WorkWelcome::returnData($sendData, $attachment_id, $work_material_id, $authCorp->id);
				} elseif ($sending->is_redpacket == 1) {
					if ($sending->redpacket_amount - $sending->send_amount >= 0.3){
						$sendData = 1;
					}
				}

				if ($sendData) {
					$sendIds                     = $data['sendId'];
//					if (count($external_userid) > 10000) {
//						$msgid                       = '';
//						$len = count($external_userid);
//						$l   = ceil($len / 10000);   // 0 1 2 3
//						for ($i = 0; $i < $l; $i++) {
//							$externalData = array_slice($external_userid, $i * 10000, 10000);
//							if (!empty($externalData)) {
//								$contact   = WorkExternalContact::find()->where(['external_userid' => $externalData])->select('id')->asArray()->all();
//								$contactId = array_column($contact, 'id');
//								$fail_list                   = '';
//								$fail_num                    = 0;
//								foreach ($sendIds as $sendId) {
//									$userId   = '';
//									$workUser = WorkUser::findOne(['corp_id' => $authCorp->id, 'userid' => $sendId]);
//									if (!empty($workUser)) {
//										$userId = $workUser->id;
//									}
//									$externalUserid              = WorkExternalContactFollowUser::getUserSendData($contactId, $sendId);
//									$sendData['external_userid'] = $externalUserid;
//									$sendData['sender']          = $sendId;
//
//									foreach ($externalUserid as $external_id) {
//										$contact = WorkExternalContact::findOne(['external_userid' => $external_id, 'corp_id' => $authCorp->id]);
//										if (!empty($contact)) {
//											$groupData['send_id']     = $this->work_group_sending_id;
//											$groupData['external_id'] = $contact->id;
//											$groupData['user_id']     = $userId;
//											WorkTagGroupStatistic::add($groupData);
//										}
//									}
//									\Yii::error($sendData, '$sendDataJob1');
//									try {
//										$sendData1 = ExternalContactMsgTemplate::parseFromArray($sendData);
//										$result   = $workApi->ECAddMsgTemplate($sendData1);
//										\Yii::error($result, '$result-externalSend11');
//										$sending = WorkGroupSending::findOne($this->work_group_sending_id);
//										if ($result['errcode'] == 0) {
//											$msgid .= $result['msgid'] . ',';
//										}
//										if (!empty($result['fail_list'])) {
//											$fail_list = implode(',', $result['fail_list']);
//											$fail_num  = count($result['fail_list']);
//										}
//									} catch (\Exception $e) {
//										\Yii::error($e->getMessage(), 'msg');
//									}
//
//								}
//								$sending->status       = 3;
//								$sending->queue_id     = 0;
//								$sending->fail_list    = $fail_list;
//								$sending->success_list = trim($msgid, ',');
//								$sending->push_time    = DateUtil::getCurrentTime();
//								$sending->save();
//							}
//						}
//					}else{
						$contact   = WorkExternalContact::find()->where(['external_userid' => $external_userid])->select('id')->asArray()->all();
						$contactId = array_column($contact, 'id');
						$fail_list = '';
						$fail_num  = 0;
						$msgid     = '';
						foreach ($sendIds as $sendId) {
							$userId   = '';
							$workUser = WorkUser::findOne(['corp_id' => $authCorp->id, 'userid' => $sendId, 'is_del' => WorkUser::USER_NO_DEL]);
							if (!empty($workUser)) {
								$userId = $workUser->id;
							}
							if(!in_array($userId,$uId)){
								continue;
							}
							$externalUserIdNew = [];
							foreach ($workExternalUserData as $data){
								if($data['user_id'] == $userId){
									array_push($externalUserIdNew,$data['wid']);
								}
							}
							$externalUserid = $externalUserIdNew;
							\Yii::error($externalUserIdNew,'$externalUserIdNew');
							if (empty($externalUserid)) {
								continue;
							}

							if ($sending->is_redpacket == 1) {
								if ($sending->redpacket_amount - $sending->send_amount >= 0.3){
									$sendData = WorkGroupSending::sendRedpacketContent($authCorp->corpid, $sending, $workUser->id);
								}
							}

							\Yii::error($sendId, '$sendId');
							$tagUserStatistic = WorkTagGroupUserStatistic::findOne(['user_id' => $userId, 'send_id' => $this->work_group_sending_id]);
							$times            = $tagUserStatistic->times;
							$sendUser         = WorkGroupSendingUser::findOne(['times' => $times + 1, 'user_id' => $userId, 'send_id' => $this->work_group_sending_id]);
							$hasSend = false;
							if ($interval == 2) {
								$intervalNum             = $sending->interval_num;
								$externalUserid          = array_slice($externalUserid, $times * $intervalNum, $intervalNum);
								\Yii::error($externalUserid,'$externalUserid');
								$tagUserStatistic->times = $tagUserStatistic->times + 1;
								$tagUserStatistic->save();
								if (!empty($sendUser)) {
									$sendUser->status = 1;
									$sendUser->save();
								}
							} else {
								$maxLength = 10000;
								$length    = count($externalUserid);
								if ($length > $maxLength) {
									$hasSend = true;
									$number = ceil($length / $maxLength);   // 0 1 2 3

									for ($i = 0; $i < $number; $i++) {
										$externalUserid = array_slice($externalUserid, $i * $maxLength, $maxLength);

										if (empty($externalUserid)) {
											continue;
										}

										$this->send($msgid, $fail_list, $fail_num, $externalUserid, $sendId, $authCorp, $userId, $workApi, $interval, $sendUser, $sendData);
									}
								}
							}

							if (!$hasSend) {
								if (empty($externalUserid)) {
									continue;
								}

								$this->send($msgid, $fail_list, $fail_num, $externalUserid, $sendId, $authCorp, $userId, $workApi, $interval, $sendUser, $sendData);
							}
						}
						if ($sending->status != 2) {
							if ($interval == 1) {
								if (!empty($sending->success_list)) {
									if (empty($msgid)) {
										$msgid = $sending->success_list;
									} else {
										$msgid = $msgid . $sending->success_list;
									}
								}
								$sending->fail_list    = $fail_list;
								$sending->success_list = trim($msgid, ',');
							}
							$sending->queue_id = 0;
							$sending->status   = 3;
							$sending->save();
						}

//					}

				}
			} catch (\Exception $e) {
				$sending = WorkGroupSending::findOne($this->work_group_sending_id);
				$message = $e->getMessage();
				if (strlen($message) >= 255) {
					$message = substr($message, 0, 200);
				}
				if (strpos($message, '40096') !== false) {
					$message = '外部联系人userid不合法';
				}
				if (strpos($message, '90207') !== false) {
					$message = '无效的appid';
				}
				if (strpos($message, '48002') !== false) {
					$message = 'API接口无权限调用';
				}
				if (strpos($message, '90208') !== false) {
					$message = '小程序appid不匹配';
				}
				if (strpos($message, '90206') !== false) {
					$message = '小程序未关联到企业中';
				}
				if (strpos($message, '81013') !== false) {
					$message = 'UserID、部门ID、标签ID全部非法或无权限';
				}
				if (strpos($message, '41048') !== false) {
					$message = '无可发送的客户';
				}
				$sending->error_msg = $message;
				$sending->status    = 2;
				$sending->queue_id  = 0;
				$sending->save();
				\Yii::error($e->getMessage(), 'externalSend');
			}
		}

		/**
		 * @param $msgid
		 * @param $fail_list
		 * @param $fail_num
		 * @param $externalUserid
		 * @param $sendId
		 * @param $authCorp
		 * @param $userId
		 * @param $workApi
		 * @param $interval
		 * @param $sendUser
		 * @param $sendData
		 */
		private function send (&$msgid, &$fail_list, &$fail_num, $externalUserid, $sendId, $authCorp, $userId, $workApi, $interval, $sendUser, $sendData)
		{
			if (!empty($externalUserid)) {
				$sendData['external_userid'] = $externalUserid;
				$sendData['sender']          = $sendId;

				$groupSending = WorkGroupSending::findOne($this->work_group_sending_id);
				if ($groupSending->is_redpacket){
					if ($groupSending->rule_id > 0) {
						$redRule = RedPackRule::find()->andWhere(['id' => $groupSending->rule_id])->asArray()->one();
					} else {
						$redRule = json_decode($groupSending->rule_text, true);
					}
				}

				foreach ($externalUserid as $external_id) {
					$contact = WorkExternalContact::findOne(['external_userid' => $external_id, 'corp_id' => $authCorp->id]);
					if (!empty($contact)) {
						$groupData['send_id']     = $this->work_group_sending_id;
						$groupData['external_id'] = $contact->id;
						$groupData['user_id']     = $userId;
						$statisticId = WorkTagGroupStatistic::add($groupData);

						if ($groupSending->is_redpacket) {
							$redpacketSend                    = [];
							$redpacketSend['corp_id']         = $authCorp->id;
							$redpacketSend['send_id']         = $this->work_group_sending_id;
							$redpacketSend['group_send_id']   = $statisticId;
							$redpacketSend['user_id']         = $userId;
							$redpacketSend['external_userid'] = $contact->id;
							$redpacketSend['rule_id']         = $groupSending->rule_id;
							$redpacketSend['is_chat']         = 0;
							$redpacketSend['send_money']      = $groupSending->redpacket_amount;
							WorkGroupSendingRedpacketSend::setData($redpacketSend, $redRule);
						}
					}
				}

				\Yii::error($sendData, '$sendDataJob1');
				try {
					$sendData1 = ExternalContactMsgTemplate::parseFromArray($sendData);
					$result    = $workApi->ECAddMsgTemplate($sendData1);
					\Yii::error($result, '$result-externalSend11');
					if ($result['errcode'] == 0) {
						$msgid .= $result['msgid'] . ',';
						if ($interval == 2 && !empty($sendUser)) {
							$sendUser->msgid = $result['msgid'];
							$sendUser->save();
						}
					}
					if (!empty($result['fail_list'])) {
						$fail_list = implode(',', $result['fail_list']);
						$fail_num  = count($result['fail_list']);
					}
				} catch (\Exception $e) {
					\Yii::error($e->getMessage(), 'msg');
					$message = $e->getMessage();
					if (strpos($message, '40096') !== false) {
						$message = '外部联系人userid不合法';
					}
					if (strpos($message, '90207') !== false) {
						$message = '无效的appid';
					}
					if (strpos($message, '48002') !== false) {
						$message = 'API接口无权限调用';
					}
					if (strpos($message, '90208') !== false) {
						$message = '小程序appid不匹配';
					}
					if (strpos($message, '90206') !== false) {
						$message = '小程序未关联到企业中';
					}
					if (strpos($message, '81013') !== false) {
						$message = 'UserID、部门ID、标签ID全部非法或无权限';
					}
					if (strpos($message, '41048') !== false) {
						$message = '无可发送的客户';
					}
					if (strpos($message, '-1') !== false) {
						$message = '系统繁忙';
					}
					if (strlen($message) >= 255) {
						$message = substr($message, 0, 200);
					}
					\Yii::error($message, 'msg$message');
					if ($interval == 2) {
						if (!empty($sendUser)) {
							$sendUser->push_type = 1;
							$sendUser->error_msg = $message;
							$sendUser->save();
						}
					} else {
//									$sending->error_msg = $message;
//									$sending->status    = 2;
//									$sending->queue_id  = 0;
//									$sending->save();
					}
				}
			}
		}

		/**
		 * 客户群群发
		 *
		 * @param $authCorp
		 * @param $data
		 *
		 * @throws \Throwable
		 */
		private function externalChat ($authCorp, $data)
		{
			$sending = WorkGroupSending::findOne($this->work_group_sending_id);
			$success = 0;
			$num     = 0;
			$message = '';
			$fail_list = '';
			$msgid = '';
			try {
				\Yii::error($data['sendIds'], 'sendIds');
				$workApi = WorkUtils::getWorkApi($authCorp->id, WorkUtils::EXTERNAL_API);

				if (!empty($data['sendIds'])) {
					//发送内容
					if ($sending->is_redpacket == 0){
						$content          = $data['content'];
						$attachment_id    = $data['attachment_id'];
						$work_material_id = $data['work_material_id'];
						$sendContentData  = json_decode($content, true);
						$sendContentData  = WorkWelcome::returnData($sendContentData, $attachment_id, $work_material_id, $authCorp->id);
					}

					if ($sending->is_redpacket){
						if ($sending->rule_id > 0) {
							$redRule = RedPackRule::find()->andWhere(['id' => $sending->rule_id])->asArray()->one();
						} else {
							$redRule = json_decode($sending->rule_text, true);
						}
					}

					foreach ($data['sendIds'] as $sendId) {
						$workUser = WorkUser::findOne(['corp_id' => $authCorp->id, 'userid' => $sendId, 'is_del' => WorkUser::USER_NO_DEL]);
						if (!empty($workUser)) {
							$chatCount = WorkChat::find()->where(['corp_id' => $authCorp->id, 'owner_id' => $workUser->id, 'group_chat' => 0])->andWhere(['status' => [0, 1, 2, 3]])->count();
							WorkTagGroupUserStatistic::add('', $workUser->id, $chatCount, $sending->id);
						}

						$chatIds = !empty($sending->chat_ids) ? json_decode($sending->chat_ids, true) : [];
						if (!empty($chatIds)) {
							foreach ($chatIds as $chatId) {
								$chat = WorkChat::findOne(['owner_id'=>$workUser->id,'id'=>$chatId]);
								if(!empty($chat)){
									$groupData['send_id'] = $this->work_group_sending_id;
									$groupData['chat_id'] = $chatId;
									$groupData['user_id'] = $workUser->id;
									$statisticId = WorkTagGroupStatistic::add($groupData, 1);

									if ($sending->is_redpacket){
										$redpacketSend                    = [];
										$redpacketSend['corp_id']         = $authCorp->id;
										$redpacketSend['send_id']         = $this->work_group_sending_id;
										$redpacketSend['group_send_id']   = $statisticId;
										$redpacketSend['user_id']         = $workUser->id;
										$redpacketSend['external_userid'] = $chatId;
										$redpacketSend['rule_id']         = $sending->rule_id;
										$redpacketSend['is_chat']         = 1;
										$redpacketSend['send_money']      = $sending->redpacket_amount;
										WorkGroupSendingRedpacketSend::setData($redpacketSend, $redRule);
									}
								}
							}
						}

						if ($sending->is_redpacket == 1) {
							$sendContentData = WorkGroupSending::sendRedpacketContent($authCorp->corpid, $sending, $workUser->id, 1);
						}

						$sendContentData['chat_type']       = 'group';
						$sendContentData['sender']          = $sendId;
						$sendContentData['external_userid'] = [];
						\Yii::error($sendContentData, '$sendData-result');
						$sendData = ExternalContactMsgTemplate::parseFromArray($sendContentData);
						$result   = $workApi->ECAddMsgTemplate($sendData);
						\Yii::error($result, 'externalChat-result');
						if ($result['errcode'] == 0) {
							$msgid .= $result['msgid'] . ',';
						}
						if (!empty($result['fail_list'])) {
							$fail_list = implode(',', $result['fail_list']);
						}
					}
					$sending->status       = 3;
					$sending->queue_id     = 0;
					$sending->fail_list    = $fail_list;
					$sending->success_list = trim($msgid, ',');
					$sending->save();
				}
			} catch (\Exception $e) {
				$message = $e->getMessage();
				\Yii::error($message, 'externalChat-$message');
				if (strpos($message, '40096') !== false) {
					$message = '外部联系人userid不合法';
				}
				if (strpos($message, '90207') !== false) {
					$message = '无效的appid';
				}
				if (strpos($message, '48002') !== false) {
					$message = 'API接口无权限调用';
				}
				if (strpos($message, '90208') !== false) {
					$message = '小程序appid不匹配';
				}
				if (strpos($message, '90206') !== false) {
					$message = '小程序未关联到企业中';
				}
				if (strpos($message, '81013') !== false) {
					$message = 'UserID、部门ID、标签ID全部非法或无权限';
				}
				if (strlen($message) >= 255) {
					$message = substr($message, 0, 200);
				}
				$sending->status    = 2;
				$sending->queue_id  = 0;
				$sending->error_msg = $message;
				$sending->save();
				\Yii::error($e->getMessage(), 'externalChat');
			}
		}

		/**
		 * @param array    $toUser
		 * @param int      $agentId
		 * @param string   $messageContent
		 * @param WorkCorp $authCorp
		 * @param int      $msg_type
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		private function messageSend ($toUser, $agentId, $messageContent, $authCorp, $msg_type)
		{
			$workApi = WorkUtils::getAgentApi($authCorp->id, $agentId);
			switch ($msg_type) {
				case 1:
					$messageContent = [
						'content' => $messageContent,
					];
					$messageContent = TextMesssageContent::parseFromArray($messageContent);
					break;
				case 2:
					$messageContent = [
						'media_id' => $messageContent,
					];
					$messageContent = ImageMesssageContent::parseFromArray($messageContent);
					break;
				case 5:
					$messageContent = [
						'articles' => [$messageContent],
					];
					$messageContent = NewsMessageContent::parseFromArray($messageContent);
					break;
				case 3:
					$messageContent = [
						'media_id' => $messageContent,
					];
					$messageContent = VoiceMesssageContent::parseFromArray($messageContent);
					break;
				case 4:
					$messageContent = [
						'media_id' => $messageContent,
					];
					$messageContent = VideoMesssageContent::parseFromArray($messageContent);
					break;
				case 6:
					$messageContent = [
						'appid' => $messageContent['appid'],
						'page'  => $messageContent['page'],
						'title' => $messageContent['title'],
					];
					$messageContent = MiniprogramNoticeMessageContent::parseFromArray($messageContent);
					break;
				case 7:
					$messageContent = [
						'media_id' => $messageContent,
					];
					$messageContent = FileMesssageContent::parseFromArray($messageContent);
					break;
			}
			$agent   = WorkCorpAgent::findOne($agentId);
			$message = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'duplicate_check_interval' => 10,
			];
			$message = Message::pareFromArray($message);
			try {
				$result             = $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
				$sending            = WorkGroupSending::findOne($this->work_group_sending_id);
				$sending->push_time = DateUtil::getCurrentTime();
				$file_num           = 0;
				if ($result['errcode'] == 81013) {
					$sending->error_code = 81013;
					$sending->error_msg  = '全部接收人无权限或不存在';
				}
				if ($result['errcode'] == 301002) {
					$sending->error_code = 301002;
					$sending->error_msg  = '无权限操作指定的应用';
				}
				if ($result['errcode'] == 48002) {
					$sending->error_code = 48002;
					$sending->error_msg  = 'API接口无权限调用';
				}
				if ($result['errcode'] == 0) {
					$sending->success_list = implode(',', $toUser);
				}
				if (!empty($result['invaliduser'])) {
					$sending->fail_list = $result['invaliduser'];
					$invalid_user       = explode('|', $result['invaliduser']);
					$file_num           = count($invalid_user);
				}
				$sending->status   = 1;
				$sending->queue_id = 0;
				$sending->real_num = $sending->will_num - $file_num;
				$sending->save();

			} catch (\Exception $e) {
				$sending           = WorkGroupSending::findOne($this->work_group_sending_id);
				$sending->status   = 2;
				$sending->queue_id = 0;
				$message           = $e->getMessage();
				if (strlen($message) >= 255) {
					$message = substr($message, 0, 200);
				}
				$sending->error_msg = $message;
				if (strpos($message, '90200') !== false) {
					$sending->error_msg = '缺少小程序appid参数';
				}
				if (strpos($message, '90201') !== false) {
					$sending->error_msg = '小程序通知的content_item个数超过限制';
				}
				if (strpos($message, '90201') !== false) {
					$sending->error_msg = '小程序通知的content_item个数超过限制';
				}
				if (strpos($message, '90206') !== false) {
					$sending->error_msg = '小程序未关联到企业中';
				}
				if (strpos($message, 'appid can not be empty string') !== false) {
					$sending->error_msg = '小程序appid不能为空';
				}
				if (strpos($message, 'title must between 4 and 12') !== false) {
					$sending->error_msg = '小程序标题必须为4到12个字';
				}
				if (strpos($message, '81013') !== false) {
					$sending->error_msg = 'UserID、部门ID、标签ID全部非法或无权限';
				}
				//$sending->error_msg = "当前用户非法，或无权限";
				$sending->save();
				\Yii::error($e->getMessage(), 'messageSend');
			}
		}
	}