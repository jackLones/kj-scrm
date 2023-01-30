<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_public_activity_fans_user_detail}}".
	 *
	 * @property int                $id
	 * @property int                $activity_id      活动id
	 * @property int                $type             方式1老用户，2取关删除，3取关+删除，4删除
	 * @property int                $user_id          企业人员id
	 * @property int                $public_parent_id 二维码归属人
	 * @property int                $public_user_id   当前公众号人物
	 * @property int                $external_userid  外部联系人id
	 * @property int                $fans_id          公众号粉丝id
	 * @property int                $is_remind        是否提醒0未提醒，1已提醒
	 * @property int                $level_time       公众号取关||外部用户删除当前员工时间
	 * @property int                $create_time      创建时间
	 * @property int                $update_time      修改时间
	 *
	 * @property WorkPublicActivity $activity
	 */
	class WorkPublicActivityFansUserDetail extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_fans_user_detail}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['activity_id', 'type', 'user_id', 'is_remind', 'fans_id', 'public_parent_id', 'public_user_id', 'external_userid', 'level_time', 'create_time', 'update_time'], 'integer'],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'               => Yii::t('app', 'ID'),
				'activity_id'      => Yii::t('app', '活动id'),
				'type'             => Yii::t('app', '方式1老用户，2取关删除，3取关+删除，4删除'),
				'user_id'          => Yii::t('app', '企业人员id'),
				'public_parent_id' => Yii::t('app', '二维码归属人	'),
				'public_user_id'   => Yii::t('app', '当前公众号人物'),
				'external_userid'  => Yii::t('app', '外部联系人id'),
				'fans_id'          => Yii::t('app', '公众号粉丝id'),
				'is_remind'        => Yii::t('app', '是否提醒0未提醒，1已提醒'),
				'level_time'       => Yii::t('app', '公众号取关||外部用户删除当前员工时间'),
				'create_time'      => Yii::t('app', '创建时间'),
				'update_time'      => Yii::t('app', '修改时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getActivity ()
		{
			return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
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
		 * @param $data
		 *设置发送明细并返回自增id
		 *
		 * @return int
		 */
		public static function setData ($data)
		{
			$row = new self();
			$row->setAttributes($data);
			$row->save();

			return $row->id;
		}
	}
