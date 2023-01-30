<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%interact_statistic}}".
	 *
	 * @property int           $id
	 * @property string        $name        公众号名称
	 * @property int           $inter_id    智能互动表id
	 * @property int           $send_num    发送次数
	 * @property int           $receive_num 接收次数
	 * @property string        $date_time   统计时间
	 *
	 * @property InteractReply $inter
	 */
	class InteractStatistic extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%interact_statistic}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['inter_id', 'send_num', 'receive_num'], 'integer'],
				[['name'], 'string', 'max' => 64],
				[['date_time'], 'safe'],
				[['inter_id'], 'exist', 'skipOnError' => true, 'targetClass' => InteractReply::className(), 'targetAttribute' => ['inter_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'name'        => Yii::t('app', '公众号名称'),
				'inter_id'    => Yii::t('app', '智能互动表id'),
				'send_num'    => Yii::t('app', '发送次数'),
				'receive_num' => Yii::t('app', '接收次数'),
				'date_time'   => Yii::t('app', '统计时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getInter ()
		{
			return $this->hasOne(InteractReply::className(), ['id' => 'inter_id']);
		}

		public function dumpData ()
		{
			$result = [
				'name'        => $this->name,
				'send_num'    => $this->send_num,
				'receive_num' => $this->receive_num,
				'date_time'   => $this->date_time,
			];

			return $result;
		}
	}
