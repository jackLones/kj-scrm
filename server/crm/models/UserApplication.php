<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%user_application}}".
	 *
	 * @property int    $id
	 * @property int    $uid             用户id
	 * @property string $merchant        商户名称
	 * @property string $license         营业执照号
	 * @property string $license_cp      营业执照照片
	 * @property string $organization_cp 组织机构代码证件照
	 * @property string $possessor_type  证件持有人类型
	 * @property string $possessor       证件持有人姓名
	 * @property string $id_number       证件号码
	 * @property string $id_cp_a         证件照正面
	 * @property string $id_cp_b         证件照反面
	 * @property string $id_cp_c         手持身份证照片
	 * @property int    $status          客户资料状态：1未审核，2审核通过，3审核失败
	 * @property int    $addtime         提交时间
	 * @property int    $pass_time       审核通过时间
	 * @property string $update_time     更新时间
	 * @property string $remark          审核备注
	 */
	class UserApplication extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%user_application}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid'], 'required'],
				[['uid', 'status', 'addtime', 'pass_time'], 'integer'],
				[['update_time'], 'safe'],
				[['merchant', 'license_cp', 'organization_cp', 'id_cp_a', 'id_cp_b', 'id_cp_c'], 'string', 'max' => 255],
				[['license', 'possessor_type', 'possessor', 'id_number'], 'string', 'max' => 30],
				[['remark'], 'string', 'max' => 1000],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'              => Yii::t('app', 'ID'),
				'uid'             => Yii::t('app', '用户id'),
				'merchant'        => Yii::t('app', '商户名称'),
				'license'         => Yii::t('app', '营业执照号'),
				'license_cp'      => Yii::t('app', '营业执照照片'),
				'organization_cp' => Yii::t('app', '组织机构代码证件照'),
				'possessor_type'  => Yii::t('app', '证件持有人类型'),
				'possessor'       => Yii::t('app', '证件持有人姓名'),
				'id_number'       => Yii::t('app', '证件号码'),
				'id_cp_a'         => Yii::t('app', '证件照正面'),
				'id_cp_b'         => Yii::t('app', '证件照反面'),
				'id_cp_c'         => Yii::t('app', '手持身份证照片'),
				'status'          => Yii::t('app', '客户资料状态：1未审核，2审核通过，3审核失败'),
				'addtime'         => Yii::t('app', '提交时间'),
				'pass_time'       => Yii::t('app', '审核通过时间'),
				'update_time'     => Yii::t('app', '更新时间'),
				'remark'          => Yii::t('app', '审核备注'),
			];
		}
	}
