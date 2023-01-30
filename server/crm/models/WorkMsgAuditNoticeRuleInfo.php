<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\components\InvalidParameterException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_notice_rule_info}}".
	 *
	 * @property int                    $id
	 * @property int                    $rule_id     规则ID
	 * @property int                    $category_id 类别ID
	 * @property int                    $agent_id    应用ID
	 * @property int                    $user_id     成员ID
	 * @property int                    $status      状态：0、关闭；1、开启
	 *
	 * @property WorkUser               $user
	 * @property WorkCorpAgent          $agent
	 * @property WorkMsgAuditCategory   $category
	 * @property WorkMsgAuditNoticeRule $rule
	 */
	class WorkMsgAuditNoticeRuleInfo extends \yii\db\ActiveRecord
	{
		const OPEN_RULE = 1;
		const CLOSE_RULE = 0;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_notice_rule_info}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['rule_id', 'category_id', 'agent_id', 'user_id', 'status'], 'integer'],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent_id' => 'id']],
				[['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
				[['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAuditNoticeRule::className(), 'targetAttribute' => ['rule_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'rule_id'     => Yii::t('app', '规则ID'),
				'category_id' => Yii::t('app', '类别ID'),
				'agent_id'    => Yii::t('app', '应用ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'status'      => Yii::t('app', '状态：0、关闭；1、开启'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCategory ()
		{
			return $this->hasOne(WorkMsgAuditCategory::className(), ['id' => 'category_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getRule ()
		{
			return $this->hasOne(WorkMsgAuditNoticeRule::className(), ['id' => 'rule_id']);
		}

		/**
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
				'rule_id'     => $this->rule_id,
				'category_id' => $this->category_id,
				'agent_id'    => $this->agent_id,
				'user_id'     => $this->user_id,
				'status'      => $this->status,
			];

			if ($withCategory) {
				$result['category_name'] = $this->category->category_name;
			}

			if ($withAgent) {
				$result['agent'] = $this->agent->dumpData();
			}

			if ($withUser) {
				$result['work_user'] = $this->user->dumpData();
			}

			return $result;
		}

		/**
		 * @param $ruleId
		 * @param $ruleData
		 *
		 * @return bool
		 *
		 * @throws InvalidParameterException
		 */
		public static function create ($ruleId, $ruleData)
		{
			if (empty($ruleId) || empty($ruleData['agent_id']) || empty($ruleData['category_ids']) || empty($ruleData['users'])) {
				throw new InvalidParameterException('缺少必要参数');
			}

			$flag = false;
			self::updateAll(['status' => self::CLOSE_RULE], ['rule_id' => $ruleId]);

			$categoryIds = explode(',', $ruleData['category_ids']);
			$users       = explode(',', $ruleData['users']);

			foreach ($categoryIds as $categoryId) {
				foreach ($users as $user) {
					try {
						$ruleInfo = self::findOne([
							'rule_id'     => $ruleId,
							'category_id' => $categoryId,
							'agent_id'    => $ruleData['agent_id'],
							'user_id'     => $user,
						]);

						if (empty($ruleInfo)) {
							$ruleInfo              = new self();
							$ruleInfo->rule_id     = $ruleId;
							$ruleInfo->category_id = $categoryId;
							$ruleInfo->agent_id    = $ruleData['agent_id'];
							$ruleInfo->user_id     = $user;
						}

						$ruleInfo->status = self::OPEN_RULE;

						if (!$ruleInfo->validate() || !$ruleInfo->save()) {
							throw new InvalidDataException(SUtils::modelError($ruleInfo));
						}

						if (!$flag) {
							$flag = true;
						}
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__);
					}
				}
			}

			return $flag;
		}
	}
