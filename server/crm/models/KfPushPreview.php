<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%kf_push_preview}}".
	 *
	 * @property int  $id
	 * @property int  $fans_id     粉丝ID
	 * @property int  $random      发送随机数
	 * @property int  $expire_time 过期时间
	 *
	 * @property Fans $fans
	 */
	class KfPushPreview extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%kf_push_preview}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['fans_id', 'random', 'expire_time'], 'integer'],
				[['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fans::className(), 'targetAttribute' => ['fans_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'fans_id'     => Yii::t('app', '粉丝ID'),
				'random'      => Yii::t('app', '发送随机数'),
				'expire_time' => Yii::t('app', '过期时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getFans ()
		{
			return $this->hasOne(Fans::className(), ['id' => 'fans_id']);
		}

		//获取随机数并记录
		public static function getRandom ($fansId)
		{
			$preview     = static::findOne(['fans_id' => $fansId]);
			$time        = time();
			$expire_time = $time + 3600;
			if (!empty($preview)) {
				if ($preview->expire_time < $time) {
					$break = false;
					while (!$break) {
						$random = rand(100000, 999999);
						$info   = static::findOne(['random' => $random]);
						if (empty($info)) {
							$break = true;
						}
					}
					$preview->random = $random;
				} else {
					$random = $preview->random;
				}
				$preview->expire_time = $expire_time;
				$preview->save();
			} else {
				$break = false;
				while (!$break) {
					$random = rand(100000, 999999);
					$info   = static::findOne(['random' => $random]);
					if (empty($info)) {
						$break = true;
					}
				}
				$preview              = new KfPushPreview();
				$preview->fans_id     = $fansId;
				$preview->random      = $random;
				$preview->expire_time = $expire_time;
				$preview->save();
			}

			return $random;
		}
	}
