<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use app\util\WorkUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_msg_audit_user}}".
	 *
	 * @property int          $id
	 * @property int          $audit_id 会话存档ID
	 * @property int          $user_id  成员ID
	 * @property string       $userid   成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节
	 * @property int          $status   状态：0、禁用；1、启用
	 * @property int          $keyword_status   智能推荐状态：0未设置、1开启、2关闭
	 *
	 * @property WorkUser     $user
	 * @property WorkMsgAudit $audit
	 */
	class WorkMsgAuditUser extends \yii\db\ActiveRecord
	{
		const USER_CLOSE = 0;
		const USER_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_msg_audit_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['audit_id', 'user_id', 'status', 'keyword_status'], 'integer'],
				[['userid'], 'string', 'max' => 64],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['audit_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgAudit::className(), 'targetAttribute' => ['audit_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'             => Yii::t('app', 'ID'),
				'audit_id'       => Yii::t('app', '会话存档ID'),
				'user_id'        => Yii::t('app', '成员ID'),
				'userid'         => Yii::t('app', '成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节'),
				'status'         => Yii::t('app', '状态：0、禁用；1、启用'),
				'keyword_status' => Yii::t('app', '智能推荐状态：0未设置、1开启、2关闭'),
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
		public function getAudit ()
		{
			return $this->hasOne(WorkMsgAudit::className(), ['id' => 'audit_id']);
		}

		/**
		 * @param $corpId
		 *
		 * @return array|int[]
		 *
		 * @throws InvalidDataException
		 * @throws \ParameterError
		 * @throws \QyApiError
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getPermitUserList ($corpId)
		{
			$result   = [];
			$workCorp = WorkCorp::findOne($corpId);

			if (empty($workCorp)) {
				throw new InvalidDataException('参数不正确');
			}

			$msgAuditApi = WorkUtils::getMsgAuditApi($corpId);
			if (!empty($msgAuditApi)) {
				$userList = $msgAuditApi->GetPermitUserList();
				if (!empty($userList['ids'])) {
					self::updateAll(['status' => self::USER_CLOSE], ['audit_id' => $workCorp->workMsgAudit->id]);

					$result = self::setUser($workCorp->workMsgAudit->id, $userList['ids']);
				}
			}

			return $result;
		}

		/**
		 * @param $msgAuditId
		 * @param $userList
		 *
		 * @return int[]
		 */
		public static function setUser ($msgAuditId, $userList)
		{
			$result = [
				'count'   => 0,
				'success' => 0,
				'failed'  => 0,
				'info'    => [],
			];

			$workMsgAudit = WorkMsgAudit::findOne($msgAuditId);
			if (!empty($workMsgAudit) && !empty($userList)) {
				$result['count'] = count($userList);

				foreach ($userList as $userId) {
					try {
						$auditUser  = self::findOne(['audit_id' => $workMsgAudit->id, 'userid' => $userId]);
						$workUserId = WorkUser::getUserId($workMsgAudit->corp_id, $userId);
						if (!empty($workUserId) && empty($auditUser)) {
							$auditUser = self::findOne(['audit_id' => $workMsgAudit->id, 'user_id' => $workUserId]);
						}

						if (empty($auditUser)) {
							$auditUser = new self();
						}

						$auditUser->audit_id = $workMsgAudit->id;
						$auditUser->user_id  = !empty($workUserId) ? $workUserId : NULL;
						$auditUser->userid   = $userId;
						$auditUser->status   = self::USER_OPEN;

						if ($auditUser->dirtyAttributes) {
							if (!$auditUser->validate() || !$auditUser->save()) {
								throw new InvalidDataException(SUtils::modelError($auditUser));
							}
						}

						if (!empty($auditUser->user)) {
							array_push($result['info'], $auditUser->user->dumpData());
						}

						$result['success']++;
					} catch (\Exception $e) {
						$result['failed']++;

						Yii::error($e->getMessage(), __CLASS__ . "-" . __FUNCTION__ . ":setUser");
					}
				}
			}

			return $result;
		}

		/**
		 * 根据会话存档配置获取成员id和部门id
		 *
		 * @param $auditId 会话存档配置id
		 * @param $is_all  是否所有状态
		 *
		 * @return array[]
		 */
		public static function getUserIdDepartId ($auditId, $is_all = 0)
		{
			$auditUser = static::find()->alias('au');
			$auditUser = $auditUser->leftJoin('{{%work_user}} wu', 'au.user_id = wu.id');
			$auditUser = $auditUser->where(['au.audit_id' => $auditId]);
			if (empty($is_all)) {
				$auditUser = $auditUser->andwhere(['au.status' => 1]);
			}
			$auditUser = $auditUser->select('wu.id,wu.corp_id,wu.department');
			$auditUser = $auditUser->asArray()->all();
			if (empty($auditUser)) {
				return [];
			}
			$userIdData   = [];
			$departIdData = [];
			$corp_id      = 0;
			/**@var WorkUser $user * */
			foreach ($auditUser as $user) {
				$corp_id = $user['corp_id'];
				array_push($userIdData, $user['id']);
				if (!empty($user['department'])) {
					$depart       = explode(',', $user['department']);
					$departIdData = array_merge($departIdData, $depart);
				}
			}
			if (!empty($departIdData)) {
				$departIdData = static::getDepartIdAll($corp_id, $departIdData);
			}

			$departIdData = array_unique($departIdData);

			return ['userIdData' => $userIdData, 'departIdData' => $departIdData];
		}

		//获取上级部门
		public static function getDepartIdAll ($corp_id, $departId)
		{
			if (!is_array($departId)) {
				$departId = [$departId];
			}
			$departList  = WorkDepartment::find()->where(['corp_id' => $corp_id, 'department_id' => $departId])->groupBy('parentid')->all();
			$parentIdArr = [];
			foreach ($departList as $depart) {
				if (!empty($depart->parentid)) {
					$tempId      = static::getDepartIdAll($corp_id, $depart->parentid);
					$parentIdArr = array_merge($parentIdArr, $tempId);
				}
			}

			return array_merge($departId, $parentIdArr);
		}
	}
