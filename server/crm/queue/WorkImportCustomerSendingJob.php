<?php
	/**
	 * Create by PhpStorm
	 * User: fulu
	 * Date: 2020/7/29
	 */

	namespace app\queue;

	use app\models\WorkCorpAgent;
	use app\models\WorkImportCustomer;
	use app\models\WorkImportCustomerMsgSend;
	use app\util\WorkUtils;
	use app\models\WorkUser;
	use yii\base\BaseObject;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use yii\queue\JobInterface;

	class WorkImportCustomerSendingJob extends BaseObject implements JobInterface
	{
		public $work_import_customer_send_id;

		public function execute ($queue)
		{
			$importSend = WorkImportCustomerMsgSend::findOne($this->work_import_customer_send_id);

			try {
				$users    = [];
				$workUser = WorkUser::findOne($importSend->user_id);
				array_push($users, $workUser->userid);
				//应用id
				$importCustomer = WorkImportCustomer::findOne($importSend->import_id);
				$agentid        = $importCustomer->agentid;
				//发送内容
				$messageContent = "管理员给你分配了" . $importSend->add_num . "个好友，快去复制电话号码添加客户吧。<a href=\"" . \Yii::$app->params['web_url'] . "/h5/pages/customImport/detail?userid=$workUser->userid&agent_id=$agentid\">点击查看</a>";

				$this->messageSend($users, $agentid, $messageContent, $importCustomer->corp_id);
			} catch (\Exception $e) {
				$importSend->status = 2;
				$importSend->save();
				\Yii::error($e->getMessage(), 'workImportCustomer-messageSend');
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
			$importSend = WorkImportCustomerMsgSend::findOne($this->work_import_customer_send_id);

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
					$importSend->status = 1;
				} else {
					if ($result['errcode'] == 81013) {
						$importSend->error_code = 81013;
						$importSend->error_msg  = '全部接收人无权限或不存在';
					} elseif ($result['errcode'] == 301002) {
						$importSend->error_code = 301002;
						$importSend->error_msg  = '无权限操作指定的应用';
					} elseif ($result['errcode'] == 48002) {
						$importSend->error_code = 48002;
						$importSend->error_msg  = 'API接口无权限调用';
					}
					$importSend->status = 2;
				}

				$importSend->save();
			} catch (\Exception $e) {
				$importSend->status    = 2;
				$importSend->error_msg = $e->getMessage();

				$importSend->save();
				\Yii::error($e->getMessage(), 'workImportCustomer-messageSend');
			}
		}
	}