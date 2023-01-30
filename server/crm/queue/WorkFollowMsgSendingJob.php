<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/6/23
	 */

	namespace app\queue;

	use app\models\WorkCorpAgent;
	use app\models\WorkDepartment;
	use app\models\WorkFollowMsg;
	use app\models\WorkFollowMsgSending;
	use app\util\WorkUtils;
	use app\util\DateUtil;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use yii\queue\JobInterface;

	class WorkFollowMsgSendingJob extends BaseObject implements JobInterface
	{

		public $work_follow_msg_sending_id;

		public function execute ($queue)
		{
			$sending = WorkFollowMsgSending::findOne($this->work_follow_msg_sending_id);
			\Yii::error($this->work_follow_msg_sending_id, 'work_follow_msg_sending_id');
			try {
				$followUser = WorkFollowMsg::findOne(['id' => $sending->msg_id, 'status' => 1]);

				if (empty($followUser)) {
					$sending->is_del      = 1;
					$sending->update_time = DateUtil::getCurrentTime();
					$sending->error_msg   = '跟进提醒已关闭';
					$sending->save();

					return false;
				} else {
					//不再应用可见范围 followMsg置为关闭
					$agentUser = [];
					$agentInfo = WorkCorpAgent::findOne($followUser->agentid);
					if (!empty($agentInfo->allow_party) || !empty($agentInfo->allow_user)) {
						$department_ids = !empty($agentInfo->allow_party) ? explode(',', $agentInfo->allow_party) : [];
						$user_arr       = !empty($agentInfo->allow_user) ? explode(',', $agentInfo->allow_user) : [];
						$agentUser      = WorkDepartment::getDepartmentUser($followUser->corp_id, $department_ids, $user_arr);
					}
					if (!in_array($followUser->user_id, $agentUser)) {
						$sending->is_del      = 1;
						$sending->update_time = DateUtil::getCurrentTime();
						$sending->error_msg   = '应用对该成员不可见';
						$sending->save();

						$followUser->status   = 0;
						$followUser->upt_time = time();
						$followUser->save();

						return false;
					}

					$send_time = !empty($followUser->send_time) ? json_decode($followUser->send_time, true) : [];
					if (!in_array($sending->send_time, $send_time)) {
						$sending->is_del      = 1;
						$sending->update_time = DateUtil::getCurrentTime();
						$sending->error_msg   = '跟进提醒时间已取消';
						$sending->save();

						return false;
					}

					$messageContent = WorkFollowMsg::sendData($followUser, $sending->send_time);

					if ($messageContent) {
						$users    = [];
						$workUser = WorkUser::findOne($followUser->user_id);
						array_push($users, $workUser->userid);
						$detail = \Yii::$app->params["web_url"].WorkFollowMsg::H5_URL."?agent_id=".$followUser->agentid;
						if(mb_strlen($messageContent) > 512){
							$messageContent = mb_substr($messageContent,0,430);
							$messageContent         .= "\r\n<a href='$detail'>查看详情</a>";
						}else{
							$messageContent         .= "<a href='$detail'>查看详情</a>";
						}
						$this->messageSend($users, $sending->agentid, $messageContent, $sending->corp_id);
					}else{
						$sending->is_del      = 1;
						$sending->update_time = DateUtil::getCurrentTime();
						$sending->error_msg   = '跟进提醒消息内容为空';
						$sending->save();

						return false;
					}
				}
			} catch (\Exception $e) {
				$sending->status = 2;
				$sending->save();
				\Yii::error($e->getMessage(), 'followMsg-execute');
			}

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
				$result  = $workApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
				$sending = WorkFollowMsgSending::findOne($this->work_follow_msg_sending_id);

				if ($result['errcode'] == 0) {
					$sending->status    = 1;
					$sending->push_time = DateUtil::getCurrentTime();
				} else {
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
					$sending->status = 2;
				}

				$sending->save();
			} catch (\Exception $e) {
				$sending            = WorkFollowMsgSending::findOne($this->work_follow_msg_sending_id);
				$sending->status    = 2;
				$message            = $e->getMessage();
				$sending->error_msg = $message;

				$sending->save();
				\Yii::error($e->getMessage(), 'followMsg-messageSend');
			}
		}
	}