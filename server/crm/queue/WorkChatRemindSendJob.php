<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/7/16
	 */

	namespace app\queue;

	use app\components\InvalidParameterException;
	use app\models\LimitWordRemind;
	use app\models\WorkChat;
	use app\models\WorkChatRemind;
	use app\models\WorkChatRemindSend;
	use app\models\WorkCorpAgent;
	use app\models\WorkExternalContact;
	use app\models\WorkMsgAuditInfoCard;
	use app\models\WorkMsgAuditInfoImage;
	use app\models\WorkMsgAuditInfoLink;
	use app\models\WorkMsgAuditInfoRedpacket;
	use app\models\WorkMsgAuditInfoText;
	use app\models\WorkMsgAuditInfoVideo;
	use app\models\WorkMsgAuditInfoVoice;
	use app\models\WorkMsgAuditInfoWeapp;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use yii\queue\JobInterface;

	class WorkChatRemindSendJob extends BaseObject implements JobInterface
	{
		public $work_chat_remind_send_id;

		public function execute ($queue)
		{
			$remindSend = WorkChatRemindSend::findOne($this->work_chat_remind_send_id);

			try {
				if (empty($remindSend->send_user_id)) {
					$remindSend->error_msg = '提醒人不能为空';
					throw new InvalidParameterException('提醒人不能为空');
				} else {
					$users        = [];
					$send_user_id = json_decode($remindSend->send_user_id, true);
					$workUser     = WorkUser::find()->where(['id' => $send_user_id])->all();
					/** @var WorkUser $v */
					foreach ($workUser as $v) {
						array_push($users, $v->userid);
					}

					//应用id
					$agentid = 0;
					if ($remindSend->remind_id) {
						if (!empty($remindSend->chat_id)) {
							$chatRemind = WorkChatRemind::findOne($remindSend->remind_id);
							if (!empty($chatRemind)) {
								$agentid = $chatRemind->agentid;
							}
						} else {
							$chatRemind = LimitWordRemind::findOne($remindSend->remind_id);
							if (!empty($chatRemind)) {
								$agentid = $chatRemind->agent_id;
							}
						}
					}

					if (!empty($agentid)) {
						//发送内容
						$messageContent = $this->getMessageContent($remindSend);

						$this->messageSend($users, $agentid, $messageContent, $remindSend->corp_id);
					} else {
						$remindSend->error_msg = '未配置应用';
						throw new InvalidParameterException('未配置应用');
					}
				}
			} catch (\Exception $e) {
				$remindSend->status = 2;
				$remindSend->save();
				\Yii::error($e->getMessage(), 'workChatRemind-messageSend');
			}
		}

		/**
		 * 编辑发送内容
		 *
		 * @param WorkChatRemindSend $remindSend
		 *
		 * @return string
		 */
		private function getMessageContent ($remindSend)
		{
			$message = '';
			switch ($remindSend->from_type) {
				case 1:
					$workUser = WorkUser::findOne($remindSend->user_id);
					$message  .= '成员【' . $workUser->name . '】';
					break;
				case 2:
					if (!empty($remindSend->external_id)) {
						$externalUser = WorkExternalContact::findOne($remindSend->external_id);
						$message      .= '客户【' . rawurldecode($externalUser->name) . '】';
					} else {
						$message .= '未知成员';
					}
					break;
				case 3:
					$message .= '群机器人';
					break;
			}

			if (!empty($remindSend->chat_id)) {
				$workChat = WorkChat::findOne($remindSend->chat_id);
				$message  .= '在群聊【' . $workChat->name . '】中，';
			} else {
				$toList   = $remindSend->tolist;
				$userType = SUtils::getUserType($toList);
				switch ($userType) {
					case SUtils::IS_WORK_USER:
						$userInfo = WorkUser::findOne(['corp_id' => $remindSend->corp_id, 'userid' => $toList]);
						if (!empty($userInfo)) {
							$message .= '对成员【' . $userInfo->name . '】';
						}
						break;
					case SUtils::IS_EXTERNAL_USER:
						$contactInfo = WorkExternalContact::findOne(['corp_id' => $remindSend->corp_id, 'external_userid' => $toList]);
						if (!empty($contactInfo)) {
							$message .= '对客户【' . $contactInfo->name . '】';
						}
						break;
				}
			}

			$message .= '发送了';

			switch ($remindSend->msgtype) {
				case WorkMsgAuditInfoText::MSG_TYPE:
					$message .= '敏感词：' . $remindSend->content;
					break;
				case WorkMsgAuditInfoImage::MSG_TYPE:
					$message .= '图片';
					break;
				case WorkMsgAuditInfoVoice::MSG_TYPE:
					$message .= '语音';
					break;
				case WorkMsgAuditInfoVideo::MSG_TYPE:
					$message .= '视频';
					break;
				case WorkMsgAuditInfoCard::MSG_TYPE:
					$message .= '名片';
					break;
				case WorkMsgAuditInfoLink::MSG_TYPE:
					$message .= '链接';
					break;
				case WorkMsgAuditInfoWeapp::MSG_TYPE:
					$message .= '小程序';
					break;
				case WorkMsgAuditInfoRedpacket::MSG_TYPE:
					$message .= '红包';
					break;
			}

			return $message;
		}

		/**
		 * @param array  $toUser
		 * @param int    $agentId
		 * @param string $messageContent
		 * @param int    $corp_id
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		private function messageSend ($toUser, $agentId, $messageContent, $corp_id)
		{
			$remindSend          = WorkChatRemindSend::findOne($this->work_chat_remind_send_id);
			$remindSend->content = $messageContent;

			$workApi = WorkUtils::getAgentApi($corp_id, $agentId);

			$messageContent = [
				'content' => $messageContent,
			];
			$messageContent = TextMesssageContent::parseFromArray($messageContent);
			$agent          = WorkCorpAgent::findOne($agentId);
			$message        = [
				'touser'                   => $toUser,
				'agentid'                  => $agent->agentid,
				'messageContent'           => $messageContent,
				'duplicate_check_interval' => 10,
			];
			$message        = Message::pareFromArray($message);

			try {
				$result = $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);

				if ($result['errcode'] == 0) {
					$remindSend->status = 1;
				} else {
					if ($result['errcode'] == 81013) {
						$remindSend->error_code = 81013;
						$remindSend->error_msg  = '全部接收人无权限或不存在';
					} elseif ($result['errcode'] == 301002) {
						$remindSend->error_code = 301002;
						$remindSend->error_msg  = '无权限操作指定的应用';
					} elseif ($result['errcode'] == 48002) {
						$remindSend->error_code = 48002;
						$remindSend->error_msg  = 'API接口无权限调用';
					}
					$remindSend->status = 2;
				}

				$remindSend->save();
			} catch (\Exception $e) {
				$remindSend->status    = 2;
				$remindSend->error_msg = $e->getMessage();

				$remindSend->save();
				\Yii::error($e->getMessage(), 'workChatRemind-messageSend');
			}
		}
	}