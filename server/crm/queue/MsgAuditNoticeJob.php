<?php
	/**
	 * 短信群发发送
	 * User: Dove Chen
	 * Date: 2020/06/20
	 * Time: 17:20
	 */

	namespace app\queue;

	use app\models\WorkMsgAuditNoticeRule;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class MsgAuditNoticeJob extends BaseObject implements JobInterface
	{
		public $auditId;
		public $categoryType;
		public $content;

		public function execute ($queue)
		{
			try {
				WorkMsgAuditNoticeRule::send($this->auditId, $this->categoryType, $this->content);
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), __CLASS__ . '-' . __FUNCTION__);
			}
		}
	}