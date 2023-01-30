<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use dovechen\yii2\weWork\src\dataStructure\Message;
	use dovechen\yii2\weWork\src\dataStructure\TextMesssageContent;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_notice_rule}}".
	 *
	 * @property int                          $id
	 * @property int                          $audit_id    会话存档ID
	 * @property string                       $notice_name 规则名称
	 * @property int                          $status      状态：0、关闭；1、开启
	 * @property string                       $create_time 创建时间
	 *
	 * @property WorkMsgAudit                 $audit
	 * @property WorkMsgAuditNoticeRuleInfo[] $workMsgAuditNoticeRuleInfos
	 */
	class WorkMsgAuditNoticeRule extends \yii\db\ActiveRecord
	{
		const OPEN_RULE = 1;
		const CLOSE_RULE = 0;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_notice_rule}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['notice_name'], 'string', 'max' => 16],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'audit_id'    => Yii::t('app', '会话存档ID'),
				'notice_name' => Yii::t('app', '规则名称'),
				'status'      => Yii::t('app', '状态：0、关闭；1、开启'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAudit ()
		{
			return $this->hasOne(WorkMsgAudit::className(), ['id' => 'audit_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkMsgAuditNoticeRuleInfos ()
		{
			return $this->hasMany(WorkMsgAuditNoticeRuleInfo::className(), ['rule_id' => 'id']);
		}

		/**\
		 * @param bool $withCategory
		 * @param bool $withAgent
		 * @param bool $withUser
		 *
		 * @return array
		 */
		public function dumpData ($withCategory = false, $withAgent = false, $withUser = false)
		{
			$result = [
				'id'          => $this->id,
				'audit_id'    => $this->audit_id,
				'notice_name' => $this->notice_name,
				'status'      => $this->status,
				'create_time' => $this->create_time,
				'info'        => [],
			];

			if (!empty($this->workMsgAuditNoticeRuleInfos)) {
				foreach ($this->workMsgAuditNoticeRuleInfos as $auditNoticeRuleInfo) {
					if ($auditNoticeRuleInfo->status == WorkMsgAuditNoticeRuleInfo::OPEN_RULE) {
						if (empty($result['info'][$auditNoticeRuleInfo->agent_id])) {
							$result['info'][$auditNoticeRuleInfo->agent_id] = [
								'agent_id'    => $auditNoticeRuleInfo->agent_id,
								'agent_info'  => $auditNoticeRuleInfo->agent->dumpData(),
								'notice_info' => [],
							];
						}

						if (empty($result['info'][$auditNoticeRuleInfo->agent_id]['notice_info'][$auditNoticeRuleInfo->category_id])) {
							$result['info'][$auditNoticeRuleInfo->agent_id]['notice_info'][$auditNoticeRuleInfo->category_id] = [
								'category_id'   => $auditNoticeRuleInfo->category_id,
								'category_name' => $auditNoticeRuleInfo->category->category_name,
								'user_info'     => [],
							];
						}

						if (empty($result['info'][$auditNoticeRuleInfo->agent_id]['notice_info'][$auditNoticeRuleInfo->category_id]['user_info'][$auditNoticeRuleInfo->user_id])) {
							$result['info'][$auditNoticeRuleInfo->agent_id]['notice_info'][$auditNoticeRuleInfo->category_id]['user_info'][$auditNoticeRuleInfo->user_id] = [
								'user_id'   => $auditNoticeRuleInfo->user_id,
								'user_info' => $auditNoticeRuleInfo->user->dumpData(),
							];
						}
					}
				}
			}

			return $result;
		}

		/**
		 * @param $auditId
		 * @param $noticeData
		 *
		 * @return int
		 *
		 * @throws InvalidDataException
		 * @throws InvalidParameterException
		 */
		public static function create ($auditId, $noticeData)
		{
			if (empty($auditId) || empty($noticeData['notice_name']) || empty($noticeData['agent_id']) || empty($noticeData['category_ids']) || empty($noticeData['users'])) {
				throw new InvalidParameterException('缺少必要参数');
			}

			if (empty($noticeData['notice_id'])) {
				$ruleData = self::findOne(['audit_id' => $auditId, 'notice_name' => $noticeData['notice_name']]);

				if (!empty($ruleData)) {
					throw new InvalidDataException('规则名称不能重复');
				}

				$ruleData              = new self();
				$ruleData->audit_id    = $auditId;
				$ruleData->notice_name = $noticeData['notice_name'];
				$ruleData->status      = self::OPEN_RULE;
				$ruleData->create_time = DateUtil::getCurrentTime();

				if (!$ruleData->validate() || !$ruleData->save()) {
					throw new InvalidDataException(SUtils::modelError($ruleData));
				}
			} else {
				$ruleData = self::findOne(['id' => $noticeData['notice_id'], 'audit_id' => $auditId]);

				if (empty($ruleData)) {
					throw new InvalidDataException('修改的规则不存在');
				}
			}

			WorkMsgAuditNoticeRuleInfo::create($ruleData->id, $noticeData);

			return $ruleData->id;
		}

		public static function send ($auditId, $categoryType, $content)
		{
			$ruleData = self::findOne(['audit_id' => $auditId, 'status' => self::OPEN_RULE]);

			if (!empty($ruleData)) {
				$category = WorkMsgAuditCategory::findOne(['category_type' => $categoryType]);
				if (!empty($category)) {
					$ruleInfo = WorkMsgAuditNoticeRuleInfo::findAll(['rule_id' => $ruleData->id, 'category_id' => $category->id, 'status' => WorkMsgAuditNoticeRuleInfo::OPEN_RULE]);

					if (!empty($ruleInfo)) {
						$pushData = [];
						foreach ($ruleInfo as $info) {
							if (empty($pushData[$info->agent_id])) {
								$pushData[$info->agent_id] = [
									'agent_id' => $info->agent->agentid,
									'to_user'  => [],
								];
							}

							array_push($pushData[$info->agent_id]['to_user'], $info->user_id);
						}
					}

					if (!empty($pushData)) {
						$messageContent = [
							'content' => $content,
						];

						foreach ($pushData as $agentId => $pushInfo) {
							try {
								$agentApi = WorkUtils::getAgentApi($ruleData->audit->corp_id, $agentId);

								$messageContent = TextMesssageContent::parseFromArray($messageContent);
								$message        = [
									'touser'                   => $pushInfo['to_user'],
									'agentid'                  => $pushInfo['agent_id'],
									'messageContent'           => $messageContent,
									'duplicate_check_interval' => 10,
								];

								$message = Message::pareFromArray($message);
								$agentApi->MessageSend($message, $invalidUserIdList, $invalidPartyIdList, $invalidTagIdList);
							} catch (\Exception $e) {
								\Yii::error($e->getMessage(), 'messageSend');
							}
						}
					}
				}
			}

			return true;
		}
	}
