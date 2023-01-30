<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_user_del_follow_user}}".
	 *
	 * @property int           $id
	 * @property int           $corp_id         企业应用id
	 * @property int           $agent           1全部
	 * @property int           $type            1全部2部门3员工
	 * @property int           $user_id         员工id
	 * @property string        $department      部门id
	 * @property string        $inform_user     可看员工删除被通知人
	 * @property string        $inform_user_key 可看员工删除被通知人old
	 * @property int           $open_status     状态
	 * @property string        $frequency       频率1每次2每天早上9点汇总
	 * @property int           $create_time
	 * @property int           $update_time
	 *
	 * @property WorkCorpAgent $agent0
	 * @property WorkCorp      $corp
	 * @property WorkUser      $user
	 */
	class WorkUserDelFollowUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_user_del_follow_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'agent', 'type', 'user_id', 'open_status', 'create_time', 'update_time'], 'integer'],
				[['department', 'inform_user_key', 'inform_user'], 'string'],
				[['frequency'], 'string', 'max' => 11],
				[['agent'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorpAgent::className(), 'targetAttribute' => ['agent' => 'id']],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'corp_id'         => Yii::t('app', '企业应用id'),
				'agent'           => Yii::t('app', '1全部'),
				'type'            => Yii::t('app', '1全部2部门3员工'),
				'user_id'         => Yii::t('app', '员工id'),
				'department'      => Yii::t('app', '部门id'),
				'inform_user'     => Yii::t('app', '可看员工删除被通知人'),
				'inform_user_key' => Yii::t('app', '可看员工删除被通知人old'),
				'open_status'     => Yii::t('app', '状态'),
				'frequency'       => Yii::t('app', '频率1每次2每天早上9点汇总'),
				'create_time'     => Yii::t('app', 'Create Time'),
				'update_time'     => Yii::t('app', 'Update Time'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getAgent0 ()
		{
			return $this->hasOne(WorkCorpAgent::className(), ['id' => 'agent']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public static function repeatConstructData (&$data, $user_id)
		{
			foreach ($data as &$item) {
				if (in_array($item["id"], $user_id) && !isset($item["children"])) {
					$item["disabled"] = true;
				}
				if (isset($item["children"])) {
					self::repeatConstructData($item["children"], $user_id);
				}
			}
		}
		/**
		 * 根据成员id获取部门名称
		 *
		 * @param string $departId
		 * @param int $corpId
		 *
		 * @return string
		 */
		public static function getDepartNameByUserId ($departId, $corpId)
		{
			$departName = '';
			if (!empty($departId)) {
				$department = WorkDepartment::find()->where(['corp_id' => $corpId])->andWhere(['department_id' => explode(",", $departId)])->select('name')->asArray()->all();
				if (!empty($department)) {
					$name       = array_column($department, 'name');
					$departName = implode('/', $name);
				}
			}

			return $departName;
		}
	}
