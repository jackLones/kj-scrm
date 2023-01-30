<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/5/22
	 * Time: 15:25
	 */

	namespace app\queue;

	use app\components\InvalidDataException;
	use app\models\ExternalTimeLine;
	use app\models\WorkChat;
	use app\models\WorkChatInfo;
	use app\models\WorkCorp;
	use app\models\WorkUser;
	use app\util\SUtils;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class WorkExternalChatJob extends BaseObject implements JobInterface
	{
		public $xml;
		public $from = 1;
		public $corp_id;
		public $chat_id;
		public $type = '';

		public function execute ($queue)
		{
			if ($this->from == 1) {
				$externalChatData = SUtils::Xml2Array($this->xml);
				SUtils::arrayCase($externalChatData);
				$this->corp_id = $externalChatData['tousername'];
				$this->chat_id = $externalChatData['chatid'];
				$this->type    = $externalChatData['changetype'];

				\Yii::error($externalChatData, __CLASS__ . '-' . __FUNCTION__ . '$externalChatData');
			}

			if (empty($this->corp_id) || empty($this->chat_id)) {
				return false;
			}

			$authCorp = WorkCorp::findOne(['corpid' => $this->corp_id]);

			if (empty($authCorp) || empty($authCorp->workCorpBind)) {
				return false;
			}

			\Yii::error($this->corp_id, __CLASS__ . '-' . __FUNCTION__ . 'corp_id');
			\Yii::error($this->chat_id, __CLASS__ . '-' . __FUNCTION__ . 'chat_id');

			try {
				if ($this->type == 'dismiss') {
					//群解散
					$workChat = WorkChat::findOne(['corp_id' => $authCorp->id, 'chat_id' => $this->chat_id]);
					if (!empty($workChat) && $workChat->status != 4) {
						$workChat->status = 4;
						if (!$workChat->validate() || !$workChat->save()) {
							throw new InvalidDataException(SUtils::modelError($workChat));
						}

						WorkChatInfo::updateAll(['status' => WorkChatInfo::LEAVE_MEMBER, 'leave_time' => time()], ['chat_id' => $workChat->id, 'status' => WorkChatInfo::NORMAL_MEMBER]);
					}
				} else {
					WorkChat::getChatInfo($authCorp->id, $this->chat_id);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'WorkExternalContactJob');

				return false;
			}
		}
	}