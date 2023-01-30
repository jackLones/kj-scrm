<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_public_activity_statistic}}".
	 *
	 * @property int                $id
	 * @property int                $activity_id   活动id
	 * @property string             $time          时间
	 * @property int                $new_fans      新增
	 * @property int                $participation 净增粉丝
	 * @property int                $now_not_day   取关粉丝
	 * @property int                $net_fans      参与粉丝
	 * @property int                $success       完成任务
	 * @property int                $new_add       新添加好友
	 * @property int                $lose_fans     流失好友
	 * @property int                $keep          好友留存率
	 * @property int                $type          1天，2周，3月
	 *
	 * @property WorkPublicActivity $activity
	 */
	class WorkPublicActivityStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['keep', 'activity_id', 'new_fans', 'participation', 'now_not_day', 'net_fans', 'success', 'new_add', 'lose_fans', 'type'], 'integer'],
				[['time'], 'string', 'max' => 60],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'activity_id'   => Yii::t('app', '活动id'),
				'time'          => Yii::t('app', '时间'),
				'new_fans'      => Yii::t('app', '新增'),
				'participation' => Yii::t('app', '净增粉丝'),
				'now_not_day'   => Yii::t('app', '取关粉丝'),
				'net_fans'      => Yii::t('app', '参与粉丝'),
				'success'       => Yii::t('app', '完成任务'),
				'new_add'       => Yii::t('app', '新添加好友'),
				'lose_fans'     => Yii::t('app', '流失好友'),
				'keep'          => Yii::t('app', '好友留存率'),
				'type'          => Yii::t('app', '1天，2周，3月'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getActivity ()
		{
			return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
		}
	}
