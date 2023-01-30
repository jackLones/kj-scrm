<?php
	/**
	 * 补充会话存档客户id
	 * User: xingchangyu
	 * Date: 2020/04/22
	 * Time: 13：00
	 */

	namespace app\queue;

	use app\models\WorkMsgAudit;
	use app\models\WorkMsgAuditInfo;
	use app\models\WorkMsgAuditUser;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncMsgAuditJob extends BaseObject implements JobInterface
	{
		public $corpId;
		public $userId;

		public function execute ($queue)
		{
			\Yii::error($this->userId, 'userIdqueue');
			ini_set('memory_limit', '1024M');
			set_time_limit(0);
			if (empty($this->corpId) || empty($this->userId)) {
				return '';
			}
			$cacheKey      = 'msg_audit_job_' . $this->userId;
			$msgAuditCache = \Yii::$app->cache->get($cacheKey);
			if (!empty($msgAuditCache)) {
				return '';
			}
			\Yii::$app->cache->set($cacheKey, 1, 300);

			$workMsgAudit = WorkMsgAudit::findOne(['corp_id' => $this->corpId, 'status' => [0, 1]]);
			if (empty($workMsgAudit)) {
				return '';
			}
			$auditUser = WorkMsgAuditUser::findOne(['audit_id' => $workMsgAudit->id, 'user_id' => $this->userId]);
			if (empty($auditUser)) {
				return '';
			}

			$page     = 1;
			$pageSize = 1000;
			while (true) {
				$offset = ($page - 1) * $pageSize;
				$sql    = '
	SELECT
		`id`,
		( CASE `user_id` WHEN ' . $this->userId . ' THEN `to_external_id` ELSE `external_id` END ) AS chatUserId
	FROM
		{{%work_msg_audit_info}} 
	WHERE
		( ( ( `from_type` = ' . WorkMsgAuditInfo::IS_WORK_USER . ' ) AND ( `to_type` = ' . WorkMsgAuditInfo::IS_EXTERNAL_USER . ' ) AND ( `user_id` = ' . $this->userId . ' ) ) 
		OR ( ( `from_type` = ' . WorkMsgAuditInfo::IS_EXTERNAL_USER . ' ) AND ( `to_type` = ' . WorkMsgAuditInfo::IS_WORK_USER . ' ) AND ( `to_user_id` = ' . $this->userId . ' ) ) ) AND `msgtype` not in ("meeting_voice_call","voip_doc_share")
		having `chatUserId` is NULL LIMIT ' . $offset . ',' . $pageSize;
				\Yii::error($sql, '$sqlqueue');
				$localListInfo = WorkMsgAuditInfo::findBySql($sql)->asArray()->all();
				if (empty($localListInfo)) {
					break;
				}
				foreach ($localListInfo as $key => $listData) {
					if (empty($listData['chatUserId'])) {
						WorkMsgAuditInfo::updateExternalId($this->corpId, $listData['id']);
					}
				}

				$page++;
			}
			\Yii::$app->cache->delete($cacheKey);
		}
	}