<?php

	namespace app\models;

	use Yii;
	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;

	/**
	 * This is the model class for table "{{%work_contact_way_line}}".
	 *
	 * @property int                 $id
	 * @property int                 $way_id          渠道二维码ID
	 * @property int                 $type            1新增2客户删除员工3员工删除客户
	 * @property int                 $external_userid 外部联系人ID
	 * @property int                 $user_id         成员ID
	 * @property int                 $gender          外部联系人性别 0-未知 1-男性 2-女性
	 * @property string              $create_time     操作时间
	 *
	 * @property WorkExternalContact $externalUser
	 * @property WorkUser            $user
	 * @property WorkContactWay      $way
	 */
	class WorkContactWayLine extends \yii\db\ActiveRecord
	{
		const ADD_EXTERNAL  = 1;
		const DEL_WORK_USER = 2;
		const DEL_EXTERNAL  = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_contact_way_line}}';
		}

		/**
		 *
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['way_id', 'type', 'external_userid', 'user_id'], 'integer'],
				[['create_time'], 'safe'],
				[['external_userid'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_userid' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
				[['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => 'ID',
				'way_id'          => 'Way ID',
				'type'            => 'Type',
				'external_userid' => 'External Userid',
				'user_id'         => 'User ID',
				'create_time'     => 'Create Time',
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternalUser ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_userid']);
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
		public function getWay ()
		{
			return $this->hasOne(WorkContactWay::className(), ['id' => 'way_id']);
		}

		public function dumpData ($findLast = false)
		{
			$userInfo                  = $this->user->dumpData();
			$customInfo                = $this->externalUser->dumpData();
			$result['department_name'] = $userInfo['department_name'] . '--' . $userInfo['name'];
			$name                      = $customInfo['name'];

			if ($findLast) {
				/** @var WorkContactWayLine $lastData */
				$lastData = self::find()->where(['way_id' => $this->way_id, 'external_userid' => $this->external_userid, 'user_id' => $this->user_id])->orderBy(['create_time' => SORT_DESC])->one();

				if ($lastData->type == self::DEL_WORK_USER) {
					$name .= "（已被客户删除）";
				} elseif ($lastData->type == self::DEL_EXTERNAL) {
					$name .= "（主动删除客户）";
				}
			}

			$result['name']        = $name;
			$result['key']         = $this->id;
			$result['create_time'] = $this->create_time;

			return $result;
		}

		/**
		 * @param        $way_id
		 * @param        $type
		 * @param        $external_userid
		 * @param        $user_id
		 * @param        $gender
		 * @param string $create_time
		 *
		 */
		public static function add ($way_id, $gender, $type, $external_userid, $user_id, $create_time = '')
		{
			$info = WorkContactWayLine::findOne(['way_id' => $way_id, 'type' => $type, 'external_userid' => $external_userid, 'user_id' => $user_id]);
			if ($info === NULL) {
				$line                  = new WorkContactWayLine();
				$line->way_id          = $way_id;
				$line->type            = $type;
				$line->external_userid = $external_userid;
				$line->user_id         = $user_id;
				$line->gender          = $gender;
				if (!empty($create_time)) {
					$line->create_time = $create_time;
				} else {
					$line->create_time = DateUtil::getCurrentTime();
				}
				$line->save();
			}
		}

		/**
		 * @param $external_userid
		 * @param $gender
		 * @param $user_id
		 * @param $type
		 *
		 */
		public static function updateLine ($external_userid, $gender, $user_id, $type)
		{
			$followUser = WorkExternalContactFollowUser::find()->where(['user_id' => $user_id, 'external_userid' => $external_userid])->asArray()->all();
			if (!empty($followUser)) {
				foreach ($followUser as $user) {
					if (!empty($user['way_id'])) {
						static::add($user['way_id'], $gender, $type, $external_userid, $user_id);
					}
				}
			}
		}
	}
