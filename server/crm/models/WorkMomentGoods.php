<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_moment_goods}}".
	 *
	 * @property int                 $id
	 * @property int                 $moment_id   朋友圈ID
	 * @property int                 $user_id     成员ID
	 * @property int                 $external_id 外部联系人ID
	 * @property string              $openid      外部非联系人openid
	 * @property int                 $status      状态：0、取消点赞；1：点赞
	 * @property string              $create_time 创建时间
	 *
	 * @property WorkExternalContact $external
	 * @property WorkMoments         $moment
	 * @property WorkUser            $user
	 */
	class WorkMomentGoods extends \yii\db\ActiveRecord
	{
		const NOT_GOOD = 0;
		const HAS_GOOD = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_moment_goods}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['moment_id', 'user_id', 'external_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['openid'], 'string', 'max' => 64],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['moment_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMoments::className(), 'targetAttribute' => ['moment_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'moment_id'   => Yii::t('app', '朋友圈ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'openid'      => Yii::t('app', '外部非联系人openid'),
				'status'      => Yii::t('app', '状态：0、取消点赞；1：点赞'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getExternal ()
		{
			return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMoment ()
		{
			return $this->hasOne(WorkMoments::className(), ['id' => 'moment_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public function dumpData ($withInfo = false)
		{
			$data = [
				'moment_id'   => $this->moment_id,
				'user_id'     => $this->user_id,
				'external_id' => $this->external_id,
				'openid'      => $this->openid,
				'status'      => $this->status,
				'create_time' => $this->create_time,
			];

			if ($withInfo) {
				if (!empty($this->user_id)) {
					$data['info'] = $this->user->dumpMiniData();
				} elseif (!empty($this->external_id)) {
					$data['info'] = $this->external->dumpMiniData();
				} else {
					$data['info'] = [];
				}
			}
		}
	}
