<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%message_pack}}".
	 *
	 * @property int    $id
	 * @property int    $num         短信包条数
	 * @property string $price       当前售价
	 * @property int    $status      是否启用，1：启用、0：不启用
	 * @property string $update_time 更新时间
	 * @property string $create_time 创建时间
	 */
	class MessagePack extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%message_pack}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['num'], 'required'],
				[['num', 'status'], 'integer'],
				[['price'], 'number'],
				[['update_time', 'create_time'], 'safe'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'num'         => Yii::t('app', '短信包条数'),
				'price'       => Yii::t('app', '当前售价'),
				'status'      => Yii::t('app', '是否启用，1：启用、0：不启用'),
				'update_time' => Yii::t('app', '更新时间'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		//设置短信包
		public static function setPack ($data)
		{
			$id    = intval($data['id']);
			$num   = intval($data['num']);
			$price = trim($data['price']);
			if (empty($price) || ($price == '0.0')) {
				throw new InvalidDataException('请填写售价');
			}
			if (!empty($id)) {
				$pack = static::findOne($id);
				if (empty($pack)) {
					throw new InvalidDataException('参数不正确');
				}
				$pack->update_time = DateUtil::getCurrentTime();
			} else {
				if (empty($num)) {
					throw new InvalidDataException('请填写短信包条数');
				}
				$temp = static::findOne(['num' => $num]);
				if (!empty($temp)) {
					throw new InvalidDataException('此短信包条数已添加过，请更改条数');
				}
				$pack              = new MessagePack();
				$pack->num         = $num;
				$pack->create_time = DateUtil::getCurrentTime();
			}
			$pack->price  = $price;
			$pack->status = 1;
			if (!$pack->save()) {
				throw new InvalidDataException(SUtils::modelError($pack));
			}

			return ['error' => 0, 'msg' => ''];
		}
	}
