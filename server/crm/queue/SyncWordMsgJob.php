<?php
	/**
	 * 红包裂变状态及自动发放
	 * User: xingchangyu
	 * Date: 2020/07/23
	 * Time: 16：00
	 */

	namespace app\queue;

	use app\models\LimitWordMsg;
	use app\models\WorkMsgAuditInfo;
	use app\models\WorkMsgAuditInfoText;
	use yii\base\BaseObject;
	use yii\queue\JobInterface;

	class SyncWordMsgJob extends BaseObject implements JobInterface
	{
		public $jobData;

		public function execute ($queue)
		{
			ini_set('memory_limit', '1024M');
			set_time_limit(0);

			$corp_id    = !empty($this->jobData['corp_id']) ? $this->jobData['corp_id'] : '';
			$word_id    = !empty($this->jobData['word_id']) ? $this->jobData['word_id'] : '';
			$word_title = !empty($this->jobData['word_title']) ? $this->jobData['word_title'] : '';
			$chat_id    = !empty($this->jobData['chat_id']) ? $this->jobData['chat_id'] : '';
			$user_id    = !empty($this->jobData['user_id']) ? $this->jobData['user_id'] : '';
			$audit_id   = !empty($this->jobData['audit_id']) ? $this->jobData['audit_id'] : '';
			$uid        = !empty($this->jobData['uid']) ? $this->jobData['uid'] : '';

			if (empty($corp_id) || empty($word_id) || empty($word_title) || empty($audit_id) || empty($uid)) {
				return false;
			}
			try {
				if (!empty($chat_id)) {
					$cacheKey    = 'chat_' . $corp_id . '_' . $chat_id . '_' . $word_id;
					$ticketCache = \Yii::$app->cache->get($cacheKey);
					if (!empty($ticketCache)) {
						return false;
					}
					\Yii::$app->cache->set($cacheKey, 1, 3600);

					$wordMsg     = LimitWordMsg::find()->alias('wm');
					$wordMsg     = $wordMsg->leftJoin('{{%work_msg_audit_info}} ai', 'wm.audit_info_id = ai.id');
					$wordMsg     = $wordMsg->where(['wm.corp_id' => $corp_id, 'wm.word_id' => $word_id, 'ai.chat_id' => $chat_id, 'ai.msgtype' => 'text']);
					$wordMsgInfo = $wordMsg->select('ai.id')->orderBy(['wm.id' => SORT_DESC])->asArray()->one();

					$auditInfoId = !empty($wordMsgInfo) ? $wordMsgInfo['id'] : 0;

					//添加监控信息
					$page     = 1;
					$pageSize = 5000;
					while (true) {
						$offset    = ($page - 1) * $pageSize;
						$auditInfo = WorkMsgAuditInfo::find()->alias('ai');
						$auditInfo = $auditInfo->leftJoin('{{%work_msg_audit_info_text}} ait', 'ai.id=ait.audit_info_id');
						$auditInfo = $auditInfo->where(['ai.audit_id' => $audit_id, 'ai.msgtype' => 'text', 'ai.chat_id' => $chat_id]);
						$auditInfo = $auditInfo->andWhere(['>', 'ai.id', $auditInfoId]);
						$auditInfo = $auditInfo->andWhere(['like', 'ait.content_convert', $word_title]);
						$auditInfo = $auditInfo->limit($pageSize)->offset($offset)->all();
						if (empty($auditInfo)) {
							break;
						}

						/**@var WorkMsgAuditInfo $msgInfo * */
						foreach ($auditInfo as $msgInfo) {
							$fromType = $msgInfo->from_type;
							LimitWordMsg::setMsg(['corp_id' => $corp_id, 'word_id' => $word_id, 'audit_info_id' => $msgInfo->id, 'from_type' => $fromType, 'uid' => $uid]);

						}
						$page++;
					}
					\Yii::$app->cache->delete($cacheKey);
				} elseif (!empty($user_id)) {
					$cacheKey    = 'user_' . $corp_id . '_' . $user_id . '_' . $word_id;
					$ticketCache = \Yii::$app->cache->get($cacheKey);
					if (!empty($ticketCache)) {
						return false;
					}
					\Yii::$app->cache->set($cacheKey, 1, 3600);

					//获取最后的消息id
					$wordMsg     = LimitWordMsg::find()->alias('wm');
					$wordMsg     = $wordMsg->leftJoin('{{%work_msg_audit_info}} ai', 'wm.audit_info_id = ai.id');
					$wordMsg     = $wordMsg->where(['wm.corp_id' => $corp_id, 'wm.word_id' => $word_id, 'ai.chat_id' => NULL, 'ai.msgtype' => 'text']);
					$wordMsg     = $wordMsg->andWhere(['or', ['ai.user_id' => $user_id], ['ai.to_user_id' => $user_id]]);
					$wordMsg     = $wordMsg->andWhere(['or', ['ai.from_type' => 1, 'ai.to_type' => 2], ['ai.from_type' => 2, 'ai.to_type' => 1]]);
					$wordMsgInfo = $wordMsg->select('ai.id')->orderBy(['wm.id' => SORT_DESC])->asArray()->one();

					$auditInfoId = !empty($wordMsgInfo) ? $wordMsgInfo['id'] : 0;

					//添加监控信息
					$page     = 1;
					$pageSize = 5000;
					while (true) {
						$offset    = ($page - 1) * $pageSize;
						$auditInfo = WorkMsgAuditInfo::find()->alias('ai');
						$auditInfo = $auditInfo->leftJoin('{{%work_msg_audit_info_text}} ait', 'ai.id=ait.audit_info_id');
						$auditInfo = $auditInfo->where(['ai.audit_id' => $audit_id, 'ai.msgtype' => 'text', 'ai.chat_id' => NULL]);
						$auditInfo = $auditInfo->andWhere(['>', 'ai.id', $auditInfoId]);
						$auditInfo = $auditInfo->andWhere(['or', ['ai.user_id' => $user_id], ['ai.to_user_id' => $user_id]]);
						$auditInfo = $auditInfo->andWhere(['or', ['ai.from_type' => 1, 'ai.to_type' => 2], ['ai.from_type' => 2, 'ai.to_type' => 1]]);
						$auditInfo = $auditInfo->andWhere(['like', 'ait.content_convert', $word_title]);
						$auditInfo = $auditInfo->limit($pageSize)->offset($offset)->all();
						if (empty($auditInfo)) {
							break;
						}
						/**@var WorkMsgAuditInfo $msgInfo * */
						foreach ($auditInfo as $msgInfo) {
							$fromType = $msgInfo->from_type;
							LimitWordMsg::setMsg(['corp_id' => $corp_id, 'word_id' => $word_id, 'audit_info_id' => $msgInfo->id, 'from_type' => $fromType, 'uid' => $uid]);
						}
						$page++;
					}
					\Yii::$app->cache->delete($cacheKey);
				}
			} catch (\Exception $e) {
				\Yii::error($e->getMessage(), 'SyncWordMsgJob');
			}
		}
	}