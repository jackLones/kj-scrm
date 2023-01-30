<?php
	/**
	 * Create by PhpStorm
	 * User: wangpan
	 * Date: 2020/2/14
	 * Time: 15:14
	 */

	namespace app\queue;

	use app\models\UserCorpRelation;
	use app\models\WorkChat;
	use app\models\WorkChatInfo;
	use app\models\WorkChatWayList;
	use app\models\WorkCorpAgent;
	use app\models\WorkExternalContact;
	use app\models\WorkExternalContactFollowUser;
	use app\models\WorkTagGroupStatistic;
	use app\models\WorkTagGroupUserStatistic;
	use app\models\WorkTagPullGroup;
	use app\models\WorkWelcome;
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

	class WorkTagPullGroupJob extends BaseObject implements JobInterface
	{

		public $groupId;

		public function execute ($queue)
		{
			ini_set('memory_limit', '4096M');
			set_time_limit(0);
			$sending = WorkTagPullGroup::findOne($this->groupId);
			\Yii::error($this->groupId, 'groupId-tag-pull');
			try {
				$authCorp    = WorkCorp::findOne($sending->corp_id);
				$externalIds = [];
				$others      = json_decode($sending->others, true);
				$uids        = [];
				if (!empty($others)) {
					if (isset($others['others']['user_ids']) && !empty($others['others']['user_ids'])) {
						foreach ($others['others']['user_ids'] as $uid) {
							array_push($uids, $uid['id']);
						}
					}
				}

				if ($sending->send_type == 1) {
					$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
					$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
					$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $sending->corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
					if (!empty($uids)) {
						$workExternalUserData = $workExternalUserData->andWhere(['in', 'wf.user_id', $uids]);
					}
					if ($sending->is_filter == 1) {
						$list    = WorkChatWayList::find()->alias('w');
						$list    = $list->leftJoin('{{%work_chat}} s', '`w`.`chat_id` = `s`.`id`');
						$list    = $list->where(['w.tag_pull_id' => $sending->id, 's.corp_id' => $sending->corp_id])->select('s.id as id')->asArray()->all();
						$chat_id = [];
						if (!empty($list)) {
							foreach ($list as $val) {
								array_push($chat_id, $val['id']);
							}
						}
						if (!empty($chat_id)) {
							$users    = [];
							$chatInfo = WorkChatInfo::find()->where(['chat_id' => $chat_id, 'status' => 1, 'type' => 2])->select('external_id')->groupBy('external_id')->asArray()->all();
							if (!empty($chatInfo)) {
								foreach ($chatInfo as $info) {
									array_push($users, $info['external_id']);
								}
							}
							if (!empty($users)) {
								$workExternalUserData = $workExternalUserData->andWhere(['not in', 'wf.external_userid', $users]);
							}
						}
					}
					$workExternalUserData = $workExternalUserData->select('we.id as wid')->groupBy('we.id');
					$workExternalUserData = $workExternalUserData->asArray()->all();
					if (!empty($workExternalUserData)) {
						foreach ($workExternalUserData as $key => $val) {
							array_push($externalIds, $val['wid']);
						}
					}

				} elseif ($sending->send_type == 2) {
					$externalIds = $sending->user_key;
					$externalIds = json_decode($externalIds, true);
				}
				if (empty($externalIds)) {
					$sending->queue_id  = 0;
					$sending->error_msg = '当前发送客户为空';
					$sending->status    = 2;
					$sending->save();

					return false;
				}
				$data                    = [];
				$data['external_userid'] = $externalIds;
				$sendId                  = [];
				if (!empty($uids)) {
					foreach ($uids as $id) {
						$user = WorkUser::findOne($id);
						array_push($sendId, $user->userid);
					}

				}
				if (!empty($uids)) {
					foreach ($uids as $uid) {
						WorkTagGroupUserStatistic::add($sending->id, $uid, 0);
//
//						$workExternalUserData = WorkExternalContactFollowUser::find()->alias('wf');
//						$workExternalUserData = $workExternalUserData->leftJoin('{{%work_external_contact}} we', 'we.id=wf.external_userid');
//						$workExternalUserData = $workExternalUserData->andWhere(['we.corp_id' => $sending->corp_id, 'wf.del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
//						$workExternalUserData = $workExternalUserData->andWhere(['wf.user_id' => $uid])->select('we.id wid');
//						//\Yii::error($workExternalUserData->createCommand()->getRawSql(), 'sql');
//						$workExternalUserData = $workExternalUserData->asArray()->all();
//						if (!empty($workExternalUserData)) {
//							$num = 0;
//							foreach ($workExternalUserData as $v) {
//								if (in_array($v['wid'], $externalIds)) {
//									$num++;
//								}
//							}
//							WorkTagGroupUserStatistic::add($sending->id, $uid, $num);
//						} else {
//							WorkTagGroupUserStatistic::add($sending->id, $uid, 0);
//						}
					}
				}
				$data['sendId']  = $sendId;
				$list = WorkChatWayList::find()->where(['tag_pull_id'=>$sending->id])->asArray()->all();
				$this->externalSend($authCorp, $data,$list,$sending->content);
			} catch (\Exception $e) {
				$sending->queue_id = 0;
				$sending->status = 2;
				$sending->save();
				\Yii::error($e->getMessage(), 'execute');
			}

		}

		/**
		 * @param $authCorp
		 * @param $data
		 * @param $list
		 * @param $text_content
		 *
		 * @throws \Throwable
		 */
		private function externalSend ($authCorp, $data, $list, $text_content)
		{
			$sending = WorkTagPullGroup::findOne($this->groupId);
			$msgid = '';
			$fail = '';
			try {
				$externalUserId = $data['external_userid'];
				$key            = 0;
				if (!empty($list)) {
					foreach ($list as $li) {
						$count      = WorkChatInfo::find()->where(['chat_id' => $li['chat_id'], 'status' => 1])->count();
						$count      = 200 - $count;
						\Yii::error($count,'$count');
						$externalId = array_slice($externalUserId, $key, $key + $count);
						$key        += $count;
						if (!empty($externalId)) {
							$text['text']     = ['content' => $text_content];
							$text['image']    = ['media_id' => $li['media_id']];
							$workApi          = WorkUtils::getWorkApi($authCorp->id, WorkUtils::EXTERNAL_API);
							$attachment_id    = 0;
							$work_material_id = 0;
							$contact = WorkExternalContact::find()->where(['id'=>$externalId])->select('id')->asArray()->all();
							$contactId = array_column($contact,'id');
							$sendData         = $text;
							$sendData         = WorkWelcome::returnData($sendData, $attachment_id, $work_material_id, $authCorp->id,1);
							if ($sendData) {
								$userNewId = [];
								if(!empty($data['sendId'])){
									$sendIds = $data['sendId'];
									\Yii::error($sendIds, '$sendIds');
									\Yii::error($externalId, '$externalId');
									foreach ($sendIds as $sendId) {
										$userId   = '';
										$workUser = WorkUser::findOne(['corp_id' => $authCorp->id, 'userid' => $sendId]);
										if (!empty($workUser)) {
											$userId = $workUser->id;
										}
										$externalUserid = [];
										foreach ($contactId as $cId) {
											$groupSta = WorkTagGroupStatistic::findOne(['pull_id' => $this->groupId, 'external_id' => $cId]);
											if (empty($groupSta)) {
												$follow = WorkExternalContactFollowUser::findOne(['external_userid' => $cId, 'userid' => $sendId, 'del_type' => WorkExternalContactFollowUser::WORK_CON_EX]);
												if (!empty($follow)) {
													$contact = WorkExternalContact::findOne($cId);
													if (!empty($contact)) {
														array_push($externalUserid, $contact->external_userid);
													}
												}
											}
										}
										if(empty($externalUserid)){
											continue;
										}
										$sendData['external_userid'] = $externalUserid;
										$sendData['sender'] = $sendId;
										foreach ($externalUserid as $external_id) {
											$contact = WorkExternalContact::findOne(['external_userid' => $external_id, 'corp_id' => $authCorp->id]);
											if (!empty($contact)) {
												$groupData['pull_id']     = $this->groupId;
												$groupData['external_id'] = $contact->id;
												$groupData['chat_id']     = $li['chat_id'];
												$groupData['user_id']     = $userId;
												array_push($userNewId,$userId);
												WorkTagGroupStatistic::add($groupData);
											}
										}
										try {
											\Yii::error($sendData, '$sendData0');
											$sendData1 = ExternalContactMsgTemplate::parseFromArray($sendData);
											$result    = $workApi->ECAddMsgTemplate($sendData1);
											\Yii::error($result, '$result1');
											if ($result['errcode'] == 0) {
												$msgid .= $result['msgid'] . ',';
												if (!empty($result['fail_list'])) {
													$fail_list = implode(',', $result['fail_list']);
													$fail      .= $fail_list . ',';
												}
											}
										} catch (\Exception $e) {
											\Yii::error($e->getMessage(), 'msg');
										}

									}
									$groupSta = WorkTagGroupStatistic::find()->where(['pull_id' => $this->groupId])->select('user_id,count(id) cc')->groupBy('user_id')->asArray()->all();
									if(!empty($groupSta)){
										foreach ($groupSta as $sta){
											WorkTagGroupUserStatistic::updateAll(['will_num'=>$sta['cc']],['pull_id'=>$this->groupId,'user_id'=>$sta['user_id']]);
										}
									}
								}else{
//									$followUser = WorkExternalContactFollowUser::find()->where(['external_userid' => $externalId])->asArray()->all();
//									if (!empty($followUser)) {
//										foreach ($followUser as $user) {
//											$groupData['pull_id']     = $this->groupId;
//											$groupData['external_id'] = $user['external_userid'];
//											$groupData['user_id']     = $user['user_id'];
//											$groupData['chat_id']     = $li['chat_id'];
//											WorkTagGroupStatistic::add($groupData);
//										}
//									}
//									\Yii::error($sendData, '$sendDataTag1');
//									$sendData = ExternalContactMsgTemplate::parseFromArray($sendData);
//									$result   = $workApi->ECAddMsgTemplate($sendData);
//									\Yii::error($result, '$result2');
//									if ($result['errcode'] == 0) {
//										$msgid .= $result['msgid'] . ',';
//										//记录客户轨迹
//										//$user     = UserCorpRelation::findOne(['corp_id' => $sending->corp_id]);
//									}
								}
							}
						}
					}
					$msgid                 = trim($msgid, ',');
					$fail                  = trim($fail, ',');
					$sending->status       = 1;
					$sending->queue_id     = 0;
					$sending->success_list = $msgid;
					$sending->fail_list    = $fail;
					$sending->save();

				}
			} catch (\Exception $e) {
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
				if (strpos($message, '81013') !== false) {
					$message = 'UserID、部门ID、标签ID全部非法或无权限';
				}
				if (strpos($message, '41048') !== false) {
					$message = '无可发送的客户';
				}
				if (strlen($message) >= 255) {
					$message = substr($message, 0, 200);
				}
				$sending->error_msg = $message;
				$sending->status    = 2;
				$sending->queue_id  = 0;
				$sending->save();
				\Yii::error($e->getMessage(), 'externalSend');
			}
		}

	}