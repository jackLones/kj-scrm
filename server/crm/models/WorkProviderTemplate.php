<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_provider_template}}".
	 *
	 * @property int                $id
	 * @property int                $provider_id 服务商ID
	 * @property string             $template_id 推广包ID，最长为128个字节
	 * @property int                $status      状态：0、关闭；1、开启
	 * @property string             $create_time 创建时间
	 *
	 * @property WorkProviderConfig $provider
	 * @property WorkRegisterCode[] $workRegisterCodes
	 */
	class WorkProviderTemplate extends \yii\db\ActiveRecord
	{
		const TEMPLATE_CLOSE = 0;
		const TEMPLATE_OPEN = 1;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_provider_template}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['provider_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['template_id'], 'string', 'max' => 128],
				[['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkProviderConfig::className(), 'targetAttribute' => ['provider_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'provider_id' => Yii::t('app', '服务商ID'),
				'template_id' => Yii::t('app', '推广包ID，最长为128个字节'),
				'status'      => Yii::t('app', '状态：0、关闭；1、开启'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getProvider ()
		{
			return $this->hasOne(WorkProviderConfig::className(), ['id' => 'provider_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getWorkRegisterCodes ()
		{
			return $this->hasMany(WorkRegisterCode::className(), ['template_id' => 'id']);
		}
	}
