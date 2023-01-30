<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%message_type}}".
	 *
	 * @property int           $id
	 * @property string        $title  类型名称
	 * @property int           $status 是否启用，1：启用、0：不启用
	 *
	 * @property MessagePush[] $messagePushes
	 */
	class MessageType extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_type}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['title'], 'string', 'max' => 25],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'     => Yii::t('app', 'ID'),
				'title'  => Yii::t('app', '类型名称'),
				'status' => Yii::t('app', '是否启用，1：启用、0：不启用'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getMessagePushes ()
		{
			return $this->hasMany(MessagePush::className(), ['type_id' => 'id']);
		}

		//设置短信类型
		public static function setType ($data)
		{
			$id        = !empty($data['id']) ? $data['id'] : 0;
			$title     = !empty($data['title']) ? $data['title'] : '';
			$status    = intval($data['status']);
			$titleInfo = static::find()->where(['title' => $title]);
			if (!empty($id)) {
				$messageType = static::findOne($id);
				if (empty($messageType)) {
					throw new InvalidDataException('参数不正确');
				}
				$titleInfo = $titleInfo->andWhere(['<>', 'id', $id]);
			} else {
				$messageType = new MessageType();
			}
			$titleInfo = $titleInfo->one();
			if (!empty($titleInfo)) {
				throw new InvalidDataException('类型名称已经存在');
			}
			$messageType->title  = $title;
			$messageType->status = $status;
			if (!$messageType->save()) {
				throw new InvalidDataException(SUtils::modelError($messageType));
			}

			return ['error' => 0, 'msg' => ''];
		}
	}
