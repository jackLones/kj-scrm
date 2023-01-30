<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%default_package}}".
	 *
	 * @property int     $id
	 * @property int     $package_id        意向客户使用套餐id
	 * @property int     $duration          意向客户使用套餐时长
	 * @property int     $duration_type     时长类型 1天2月3年
	 * @property int     $expire_type       到期处理 1账号禁用2使用套餐
	 * @property int     $expire_package_id 到期客户使用套餐id(expire_type=2时)
	 * @property int     $time              时间
	 *
	 * @property Package $package
	 */
	class DefaultPackage extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%default_package}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['package_id', 'duration', 'duration_type', 'expire_type', 'expire_package_id', 'time'], 'integer'],
				[['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => Package::className(), 'targetAttribute' => ['package_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                => Yii::t('app', 'ID'),
				'package_id'        => Yii::t('app', '意向客户使用套餐id'),
				'duration'          => Yii::t('app', '意向客户使用套餐时长'),
				'duration_type'     => Yii::t('app', '时长类型 1天2月3年'),
				'expire_type'       => Yii::t('app', '到期处理 1账号禁用2使用套餐'),
				'expire_package_id' => Yii::t('app', '到期客户使用套餐id(expire_type=2时)'),
				'time'              => Yii::t('app', '时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPackage ()
		{
			return $this->hasOne(Package::className(), ['id' => 'package_id']);
		}

		//设置默认套餐
		public static function setDefaultPackage ($data)
		{
			$package_id        = !empty($data['package_id']) ? $data['package_id'] : 0;
			$duration          = !empty($data['duration']) ? $data['duration'] : '';
			$expire_type       = !empty($data['expire_type']) ? $data['expire_type'] : 1;
			$expire_package_id = !empty($data['expire_package_id']) ? $data['expire_package_id'] : 0;

			try {
				$defaultPackage = static::find()->one();
				if (empty($defaultPackage)) {
					$defaultPackage = new DefaultPackage();
				}

				$durationInfo                      = explode('_', $duration);
				$defaultPackage->package_id        = $package_id;
				$defaultPackage->duration          = $durationInfo[0];
				$defaultPackage->duration_type     = $durationInfo[1];
				$defaultPackage->expire_type       = $expire_type;
				$defaultPackage->expire_package_id = $expire_package_id;
				$defaultPackage->time              = time();

				if (!$defaultPackage->save()) {
					throw new InvalidDataException(SUtils::modelError($defaultPackage));
				}
				if ($defaultPackage->expire_type == 2 && $defaultPackage->expire_package_id){
					Package::updateAll(['is_agent' => 0], ['id' => $defaultPackage->expire_package_id]);
				}
			} catch (InvalidDataException $e) {
				throw new InvalidDataException($e->getMessage());
			}

			return ['error' => 0, 'msg' => ''];
		}
	}
