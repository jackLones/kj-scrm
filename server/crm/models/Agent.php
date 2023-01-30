<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\DateUtil;
	use app\util\SUtils;
	use Yii;

	/**
	 * This is the model class for table "{{%agent}}".
	 *
	 * @property int    $id
	 * @property int    $uid           代理商用户id
	 * @property string $aname         公司名称
	 * @property string $lxname        联系人名称
	 * @property double $discount      代理商折扣
	 * @property double $balance       账户余额
	 * @property double $cash_deposit  保证金
	 * @property int    $is_contract   是否签约1是0否
	 * @property int    $contract_time 签约时间
	 * @property int    $endtime       签约到期时间
	 * @property int    $province      所在区域
	 * @property int    $city          所在市
	 * @property int    $addtime       创建时间
	 * @property int    $upttime       修改时间
	 */
	class Agent extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%agent}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['uid', 'is_contract', 'contract_time', 'endtime', 'province', 'city', 'addtime', 'upttime'], 'integer'],
				[['discount', 'balance', 'cash_deposit'], 'number'],
				[['addtime'], 'required'],
				[['aname', 'lxname'], 'string', 'max' => 255],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'            => Yii::t('app', 'ID'),
				'uid'           => Yii::t('app', '代理商用户id'),
				'aname'         => Yii::t('app', '公司名称'),
				'lxname'        => Yii::t('app', '联系人名称'),
				'discount'      => Yii::t('app', '代理商折扣'),
				'balance'       => Yii::t('app', '账户余额'),
				'cash_deposit'  => Yii::t('app', '保证金'),
				'is_contract'   => Yii::t('app', '是否签约1是0否'),
				'contract_time' => Yii::t('app', '签约时间'),
				'endtime'       => Yii::t('app', '签约到期时间'),
				'province'      => Yii::t('app', '所在区域'),
				'city'          => Yii::t('app', '所在市'),
				'addtime'       => Yii::t('app', '创建时间'),
				'upttime'       => Yii::t('app', '修改时间'),
			];
		}

		/**
		 * 创建代理商信息
		 *
		 * @throws InvalidDataException
		 */
		public static function create ($uid, $agentData = [])
		{
			$agent = static::findOne(['uid' => $uid]);
			if (empty($agent)) {
				$agent               = new Agent();
				$agent->uid          = $uid;
				$agent->balance      = $agentData['balance'];
				$agent->cash_deposit = $agentData['cash_deposit'];
				$agent->addtime      = time();
			} else {
				$agent->upttime = time();
			}
			$agent->discount = $agentData['discount'];
			$agent->aname    = $agentData['aname'];
			$agent->lxname   = $agentData['lxname'];
			$agent->province = $agentData['province'];
			$agent->city     = $agentData['city'];

			if (!$agent->validate() || !$agent->save()) {
				throw new InvalidDataException(SUtils::modelError($agent));
			}

			return true;
		}
	}
