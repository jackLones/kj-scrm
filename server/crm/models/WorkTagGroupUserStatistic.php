<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_group_user_statistic}}".
	 *
	 * @property int              $id
	 * @property int              $user_id     员工ID
	 * @property int              $pull_id     标签拉群的ID
	 * @property int              $send_id     企业微信群发ID
	 * @property int              $will_num    预计发送人数
	 * @property int              $real_num    实际发送人数
	 * @property int              $has_num     已入群人数
	 * @property int              $status      发送状态：0未发送1已发送
	 * @property int              $times       当前员工的确认发送次数
	 * @property int              $line_type   是否存在排队发送0没有1有
	 * @property int              $push_time   发送时间
	 * @property int              $create_time 创建时间
	 *
	 * @property WorkTagPullGroup $pull
	 * @property WorkUser         $user
	 */
	class WorkTagGroupUserStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_group_user_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['user_id', 'pull_id', 'send_id', 'will_num', 'real_num', 'has_num', 'status', 'times', 'line_type', 'push_time', 'create_time'], 'integer'],
				[['pull_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTagPullGroup::className(), 'targetAttribute' => ['pull_id' => 'id']],
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
				'user_id'     => Yii::t('app', '员工ID'),
				'pull_id'     => Yii::t('app', '标签拉群的ID'),
				'send_id'     => Yii::t('app', '企业微信群发ID'),
				'will_num'    => Yii::t('app', '预计发送人数'),
				'real_num'    => Yii::t('app', '实际发送人数'),
				'has_num'     => Yii::t('app', '已入群人数'),
				'status'      => Yii::t('app', '发送状态：0未发送1已发送'),
				'times'       => Yii::t('app', '当前员工的确认发送次数'),
				'line_type'   => Yii::t('app', '是否存在排队发送0没有1有'),
				'push_time'   => Yii::t('app', '发送时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
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
		 * @return \yii\db\ActiveQuery
		 */
		public function getPull ()
		{
			return $this->hasOne(WorkTagPullGroup::className(), ['id' => 'pull_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		public static function add ($pull_id, $user_id, $will_num, $send_id = '', $line_type = 0, $times = 0)
		{
			if (!empty($pull_id)) {
				$userStatistic = static::findOne(['pull_id' => $pull_id, 'user_id' => $user_id]);
			}
			if (!empty($send_id)) {
				$userStatistic = static::findOne(['send_id' => $send_id, 'user_id' => $user_id]);
			}
			if (empty($userStatistic)) {
				$userStatistic              = new WorkTagGroupUserStatistic();
				$userStatistic->create_time = time();
				$userStatistic->pull_id     = $pull_id;
				$userStatistic->send_id     = $send_id;
				$userStatistic->user_id     = $user_id;
				$userStatistic->will_num    = $will_num;
				$userStatistic->line_type   = $line_type;
				$userStatistic->times       = $times;
				if (!$userStatistic->validate() || !$userStatistic->save()) {
					\Yii::error(SUtils::modelError($userStatistic), 'userStatistic');
				}
			}

		}

	}
