<?php

	namespace app\models;

	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%work_tag_group_statistic}}".
	 *
	 * @property int                 $id
	 * @property int                 $pull_id     自动拉群表的ID
	 * @property int                 $send_id     企业微信群发ID
	 * @property int                 $external_id 外部联系人ID
	 * @property int                 $user_id     成员ID
	 * @property int                 $chat_id     群列表ID
	 * @property int                 $status      入群状态0未入群1已入群
	 * @property int                 $send        送达状态0未收到邀请1已收到邀请2客户已不是好友3客户接收已达上限
	 * @property int                 $push_type   员工是否发送0未发送1已发送
	 * @property string              $push_time   发送时间
	 * @property string              $create_time 创建时间
	 *
	 * @property WorkExternalContact $external
	 * @property WorkTagPullGroup    $pull
	 */
	class WorkTagGroupStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_tag_group_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['pull_id', 'send_id', 'external_id', 'chat_id', 'user_id', 'status', 'send', 'push_type'], 'integer'],
				[['create_time', 'push_time'], 'safe'],
				[['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
				[['pull_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTagPullGroup::className(), 'targetAttribute' => ['pull_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'pull_id'     => Yii::t('app', '自动拉群表的ID'),
				'send_id'     => Yii::t('app', '企业微信群发ID'),
				'external_id' => Yii::t('app', '外部联系人ID'),
				'chat_id'     => Yii::t('app', '群列表ID'),
				'user_id'     => Yii::t('app', '成员ID'),
				'status'      => Yii::t('app', '入群状态0未入群1已入群'),
				'send'        => Yii::t('app', '送达状态0未收到邀请1已收到邀请2客户已不是好友3客户接收已达上限'),
				'push_type'   => Yii::t('app', '员工是否发送0未发送1已发送'),
				'push_time'   => Yii::t('app', '发送时间'),
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

		public static function add ($data, $type = 0)
		{
			$sendId = isset($data['send_id']) ? $data['send_id'] : '';
			$pullId = isset($data['pull_id']) ? $data['pull_id'] : '';
			if (!empty($sendId) && empty($type)) {
				$statistic = WorkTagGroupStatistic::findOne(['send_id' => $sendId, 'external_id' => $data['external_id'], 'user_id' => $data['user_id']]);
			}
			if (!empty($sendId) && $type == 1) {
				$statistic = WorkTagGroupStatistic::findOne(['send_id' => $sendId, 'chat_id' => $data['chat_id'], 'user_id' => $data['user_id']]);
			}
			if (!empty($pullId)) {
				$statistic = WorkTagGroupStatistic::findOne(['pull_id' => $pullId, 'external_id' => $data['external_id'], 'user_id' => $data['user_id'], 'chat_id' => $data['chat_id']]);
			}
			if (empty($statistic)) {
				$statistic              = new WorkTagGroupStatistic();
				$statistic->create_time = DateUtil::getCurrentTime();
			}
			$statistic->user_id     = $data['user_id'];
			$statistic->send_id     = $sendId;
			$statistic->pull_id     = $pullId;
			$statistic->external_id = isset($data['external_id']) ? $data['external_id'] : '';
			$statistic->chat_id     = isset($data['chat_id']) ? $data['chat_id'] : '';
			if (!$statistic->validate() || !$statistic->save()) {
				\Yii::error(SUtils::modelError($statistic), 'tagGroupStatistic');
			}

			return $statistic->id;
		}
	}
