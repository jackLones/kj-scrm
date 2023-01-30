<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%work_public_activity_prize_user}}".
	 *
	 * @property int                $id
	 * @property int                $status       发奖状态1未发送，2发送
	 * @property string             $order_sn     红包订单号
	 * @property int                $type         1实物，2红包
	 * @property string             $name         姓名
	 * @property int                $price        姓名
	 * @property int                $public_id    公众号用户id
	 * @property string             $mobile       用户留存手机
	 * @property int                $activity_id  活动id
	 * @property int                $level_id     阶段id
	 * @property int                $level        奖品等级
	 * @property string             $region       地区
	 * @property string             $city         城市
	 * @property string             $county       县
	 * @property string             $detail       详细地址
	 * @property string             $remark       备注
	 * @property int                $create_time  创建时间
	 * @property int                $update_time  修改时间
	 *
	 * @property WorkPublicActivity $activity
	 */
	class WorkPublicActivityPrizeUser extends \yii\db\ActiveRecord
	{
		const LEVEL_ONE = 1;
		const LEVEL_TWO = 2;
		const LEVEL_THREE = 3;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%work_public_activity_prize_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['status', 'public_id', 'price', 'type', 'activity_id', 'level_id', 'level', 'create_time', 'update_time'], 'integer'],
				[['name', 'region', 'city', 'county', 'order_sn'], 'string', 'max' => 60],
				[['mobile'], 'string', 'max' => 20],
				[['detail', 'remark'], 'string', 'max' => 255],
				[['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'status'      => Yii::t('app', '发奖状态1未发送，2发送'),
				'type'        => Yii::t('app', '1实物，2红包'),
				'order_sn'    => Yii::t('app', '红包订单号'),
				'name'        => Yii::t('app', '姓名'),
				'price'       => Yii::t('app', '金额'),
				'public_id'   => Yii::t('app', '公众号用户id'),
				'mobile'      => Yii::t('app', '用户留存手机'),
				'activity_id' => Yii::t('app', '活动id'),
				'level_id'    => Yii::t('app', '阶段id'),
				'level'       => Yii::t('app', '奖品等级'),
				'region'      => Yii::t('app', '地区'),
				'city'        => Yii::t('app', '城市'),
				'county'      => Yii::t('app', '县'),
				'detail'      => Yii::t('app', '详细地址'),
				'remark'      => Yii::t('app', '备注'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '修改时间'),
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
