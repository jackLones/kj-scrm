<?php
	/**
	 * Create by PhpStorm
	 * User: dovechen
	 * Date: 2020/5/28
	 * Time: 19:59
	 */

	namespace app\queue;

	use app\models\WorkCorp;
	use app\models\WorkMsgAuditInfo;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class MsgAuditSetJob extends BaseObject implements JobInterface
	{
		public $corp_id;
		public $seq = 0;
		public $msg_data;

		/**
		 * {@inheritDoc}
		 *
		 * @return bool|mixed|void
		 *
		 * @throws \Throwable
		 * @throws \app\components\InvalidDataException
		 */
		public function execute ($queue)
		{
			if (!empty($this->corp_id) && !empty($this->msg_data)) {
				$workCorp = WorkCorp::findOne($this->corp_id);

				if (empty($workCorp)) {
					return false;
				}

				if ($workCorp->workMsgAudit->seq < $this->seq) {
					$workCorp->workMsgAudit->seq = $this->seq;
					$workCorp->workMsgAudit->update();
				}

				WorkMsgAuditInfo::create($this->corp_id, $this->msg_data);
			} else {
				return false;
			}
		}
	}