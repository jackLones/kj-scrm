<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/01/08
	 */

	namespace app\queue;

	use app\models\SubUserProfile;
	use app\models\UserProfile;
	use app\models\WorkChat;
	use app\models\WorkCorpAgent;
	use app\models\WorkExternalContact;
	use app\models\WorkSop;
	use app\models\WorkSopMsgSending;
	use app\models\WorkSopTime;
	use app\util\WorkUtils;
	use app\util\DateUtil;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use yii\queue\JobInterface;

	class WorkSopMsgSendingJob extends BaseObject implements JobInterface
	{
		public $work_sop_msg_sending_id;
		public $is_remind = 0;

		public function execute ($queue)
		{
			//获取当前可发送的sop消息
			\Yii::error($this->work_sop_msg_sending_id, 'work_sop_msg_sending_ids');
			$isRemind   = $this->is_remind;//提醒操作
			$sopMsgIds  = $this->work_sop_msg_sending_id;
			$sendingNow = WorkSopMsgSending::find()->where(['id' => $sopMsgIds, 'is_del' => 0]);
			if (empty($isRemind)) {
				$sendingNow = $sendingNow->andWhere(['status' => [0, 2]]);
			}
			$sendingNow    = $sendingNow->asArray()->all();
			$sendingNowIds = [];
			foreach ($sendingNow as $v) {
				$sendingNowIds[] = $v['id'];
			}
			if (empty($sendingNowIds)) {
				return false;
			}

			$sending = WorkSopMsgSending::findOne($sendingNowIds[0]);
			\Yii::error($sending->id, 'work_sop_msg_sending_id');
			try {
				$sop = WorkSop::findOne($sending->sop_id);
				if (empty($sop) || $sop->is_del == 1 || $sop->status == 0){
					WorkSopMsgSending::updateAll(['is_del' => 1, 'update_time' => DateUtil::getCurrentTime(), 'error_msg' => 'SOP规则已关闭'], ['id' => $sendingNowIds]);

					return false;
				}
				$sopTime = WorkSopTime::findOne(['id' => $sending->sop_time_id, 'is_del' => 0]);
				if (empty($sopTime)) {
					WorkSopMsgSending::updateAll(['is_del' => 1, 'update_time' => DateUtil::getCurrentTime(), 'error_msg' => 'SOP规则时间已关闭'], ['id' => $sendingNowIds]);

					return false;
				}
				$workAgent = WorkCorpAgent::findOne(['corp_id' => $sending->corp_id, 'is_del' => WorkCorpAgent::AGENT_NO_DEL, 'close' => WorkCorpAgent::AGENT_NOT_CLOSE, 'agent_type' => WorkCorpAgent::CUSTOM_AGENT]);
				if (empty($workAgent)) {
					WorkSopMsgSending::updateAll(['is_del' => 1, 'update_time' => DateUtil::getCurrentTime(), 'error_msg' => '没有可用的应用'], ['id' => $sendingNowIds]);

					return false;
				}

				$messageContent = '';
				//不发送时间段
				if ($sop->no_send_time == 1) {
					$hourTime   = date('H:i');
					$noSendTime = json_decode($sop->no_send_time, true);
					$delaySend  = 0;
					$delayTime  = 0;
					$nowTime    = time();
					if ($noSendTime[0] < $noSendTime[1]) {
						if ($hourTime >= $noSendTime[0] && $hourTime <= $noSendTime[1]) {
							$delaySend = 1;
							$delayTime = strtotime(date('Y-m-d') . ' ' . $noSendTime[1]) - $nowTime + 60;
						}
					} elseif ($noSendTime[0] > $noSendTime[1]) {
						if ($hourTime >= $noSendTime[0] || $hourTime <= $noSendTime[1]) {
							$delaySend = 1;
							if ($hourTime >= $noSendTime[0] && $hourTime <= '23:59') {
								$delayTime = strtotime(date('Y-m-d') . ' ' . $noSendTime[1]) + 86400 - $nowTime + 60;
							} else {
								$delayTime = strtotime(date('Y-m-d') . ' ' . $noSendTime[1]) - $nowTime + 60;
							}
						}
					}

					if ($delaySend) {
						$jobId = \Yii::$app->work->delay($delayTime)->push(new WorkSopMsgSendingJob([
							'work_sop_msg_sending_id' => $sendingNowIds
						]));

						return false;
					}
				}

				//跟进状态变更不发送
				if ($sop->is_chat == 0 && $sop->type == 2) {
					$followSop     = WorkSopMsgSending::find()->alias('ms');
					$followSop     = $followSop->leftJoin("{{%work_external_contact_follow_user}} as fu", "ms.user_id = fu.user_id and ms.external_id = fu.external_userid");
					$followSop     = $followSop->andWhere(['ms.id' => $sendingNowIds, 'fu.follow_id' => $sop->follow_id]);
					$followSopData = $followSop->select('ms.id')->asArray()->all();
					$followSopIds  = array_column($followSopData, 'id');
					\Yii::error($followSopIds, 'work_sop_msg_sending_followSopIds');

					$followChangeIds = array_diff($sendingNowIds, $followSopIds);
					if (!empty($followChangeIds)) {
						WorkSopMsgSending::updateAll(['is_del' => 1, 'update_time' => DateUtil::getCurrentTime(), 'error_msg' => '客户跟进状态已变更'], ['id' => $followChangeIds]);
					}
					if (empty($followSopIds)) {
						return false;
					} else {
						$sendingNowIds = $followSopIds;
					}
				}

				$creat_name = '总经理';
				if ($sop->sub_id) {
					$subInfo    = SubUserProfile::findOne(['sub_user_id' => $sop->sub_id]);
					$creat_name = $subInfo->name;
				} else {
					if ($sop->create_user_id) {
						$workUser = WorkUser::findOne($sop->create_user_id);
						if (!empty($workUser)) {
							$creat_name = $workUser->name;
						}
					} else {
						$userInfo = UserProfile::findOne(['uid' => $sop->uid]);
						if (!empty($userInfo) && !empty($userInfo->nick_name)) {
							$creat_name = $userInfo->nick_name;
						}
					}
				}
				$messageContent .= '【' . $creat_name . '】';

				$contentData = json_decode($sending->content, true);
				$contentNum  = 0;
				foreach ($contentData as $v) {
					if (isset($v['context']) && !empty($v['context'])) {
						$contentNum++;
					}
					if ((isset($v['uploadImg']) && !empty($v['uploadImg'])) || (isset($v['uploadVideo']) && !empty($v['uploadVideo'])) || (isset($v['materialVideo']) && !empty($v['materialVideo'])) || (isset($v['uploadText']) && !empty($v['uploadText'])) || (isset($v['materialText']) && !empty($v['materialText']))){
						$contentNum++;
					}
				}

				$num          = count($sendingNowIds);
				$sendingIdStr = implode('|', $sendingNowIds);
				$sliceIds     = array_slice($sendingNowIds, 0, 2);
				if ($sop->is_chat == 0) {
					$messageContent .= '创建了个人SOP规则，提醒你给';
					$name           = '';
					foreach ($sliceIds as $key => $val) {
						if ($key == 0) {
							$externalInfo = WorkExternalContact::findOne($sending->external_id);
							$name         = '【' . $externalInfo->name_convert . '】';
						} else {
							$sliceSopSend = WorkSopMsgSending::findOne($val);
							$externalInfo = WorkExternalContact::findOne($sliceSopSend->external_id);
							$name         .= '、【' . $externalInfo->name_convert . '】';
						}
					}
					$messageContent .= $name;
					if ($num >= 3) {
						$messageContent .= '等' . $num . '位客户';
					}
					$messageContent .= '发送' . $contentNum . '条消息。';
					$url            = \Yii::$app->params['web_url'] . WorkSop::H5_URL . '?agent_id=' . $workAgent->id . '&sop_send_id=' . $sendingIdStr;
				} else {
					$messageContent .= '创建了群SOP规则，提醒你给';
					$name           = '';
					foreach ($sliceIds as $key => $val) {
						if ($key == 0) {
							$chatName = WorkChat::getChatName($sending->external_id);
							$chatName = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
							$name     = '【' . $chatName . '】';
						} else {
							$sliceSopSend = WorkSopMsgSending::findOne($val);
							$chatName     = WorkChat::getChatName($sliceSopSend->external_id);
							$chatName     = mb_strlen($chatName, "utf-8") > 14 ? mb_substr($chatName, 0, 14, 'utf-8') . '...' : $chatName;
							$name         .= '、【' . $chatName . '】';
						}
					}
					$messageContent .= $name;
					if ($num >= 3) {
						$messageContent .= '等' . $num . '个客户群';
					}
					$messageContent .= '发送' . $contentNum . '条消息。';
					$url            = \Yii::$app->params['web_url'] . WorkSop::H5_URL . '?is_chat=1&agent_id=' . $workAgent->id . '&sop_send_id=' . $sendingIdStr;
				}
				$messageContent .= "\n<a href='$url'>查看详情</a>";

				$users    = [];
				$workUser = WorkUser::findOne($sending->user_id);
				array_push($users, $workUser->userid);

				$this->messageSend($users, $workAgent->id, $messageContent, $sending->corp_id, $sendingNowIds);
			} catch (\Exception $e) {
				/*$sending->status = $sending->status == 1 ? 1 : 2;//可重复提醒发送
				$sending->save();*/

				WorkSopMsgSending::updateAll(['status' => 2], ['id' => $sendingNowIds, 'status' => [0, 2]]);
				\Yii::error($e->getMessage(), 'followMsg-execute');
			}

		}

		/**
		 * @param array  $toUser
		 * @param int    $agentId
		 * @param string $messageContent
		 * @param int    $corp_id
		 * @param array  $sendingIds
		 *
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \app\components\InvalidDataException
		 * @throws \yii\base\InvalidConfigException
		 */
		private function messageSend ($toUser, $agentId, $messageContent, $corp_id, $sendingIds)
		{
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
			$message = Message::pareFromArray($message);
			try {
				$result = $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);

				if ($result['errcode'] == 0) {
					/*if ($sending->status != 1){
						$sending->push_time = time();
					}
					$sending->status    = 1;*/

					WorkSopMsgSending::updateAll(['status' => 1, 'push_time' => time()], ['id' => $sendingIds, 'status' => [0, 2]]);
				} else {
					\Yii::error($result['errcode'], 'sopMsg-messageSend-errcode');
					$error_code = $error_msg = '';
					if ($result['errcode'] == 81013) {
						$error_code = 81013;
						$error_msg  = '全部接收人无权限或不存在';
					}
					if ($result['errcode'] == 301002) {
						$error_code = 301002;
						$error_msg  = '无权限操作指定的应用';
					}
					if ($result['errcode'] == 48002) {
						$error_code = 48002;
						$error_msg  = 'API接口无权限调用';
					}

					WorkSopMsgSending::updateAll(['status' => 2, 'error_code' => $error_code, 'error_msg' => $error_msg], ['id' => $sendingIds, 'status' => [0, 2]]);
				}

				//$sending->save();
			} catch (\Exception $e) {
				$message            = $e->getMessage();
				/*$sending->status    = $sending->status == 1 ? 1 : 2;
				$sending->error_msg = $message;

				$sending->save();*/

				WorkSopMsgSending::updateAll(['status' => 2, 'error_msg' => $message], ['id' => $sendingIds, 'status' => [0, 2]]);
				\Yii::error($e->getMessage(), 'sopMsg-messageSend');
			}
		}
	}