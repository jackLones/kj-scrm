<?php

	namespace app\models;

	use app\util\SUtils;
	use Yii;
	use yii\base\InvalidConfigException;
	use yii\db\ActiveQuery;

	/**
	 * This is the model class for table "{{%auth_store}}".
	 *
	 * @property int                             $id
	 * @property int                             $group_id    分组id
	 * @property int                             $pid         上级店铺
	 * @property int                             $uid         主账户id
	 * @property int                             $sub_id      子账户id
	 * @property int                             $corp_id     企业微信id
	 * @property int                             $manger_id   店长id
	 * @property string                          $shop_name   店铺名称
	 * @property string                          $describe    店铺描述
	 * @property int                             $status      店铺状态
	 * @property string                          $auth_id     店铺权限
	 * @property string                          $province    省
	 * @property string                          $city        市
	 * @property string                          $district    区|县
	 * @property string                          $address     地址
	 * @property string                          $lat         纬度
	 * @property string                          $lng         经度
	 * @property string                          $qc_url      渠道码
	 * @property string                          $config_id   渠道码config
	 * @property int                             $is_del      0未删除1删除
	 * @property string                          $create_time 创建时间
	 * @property string                          $update_time 创建时间
	 *
	 * @property WorkCorp                        $corp
	 * @property AuthStoreGroup                  $group
	 * @property WorkUser                        $manger
	 * @property SubUser                         $sub
	 * @property AuthStoreUser[]                 $authStoreUsers
	 * @property WorkUser[]                      $users
	 * @property WorkExternalContactFollowUser[] $workExternalContactFollowUsers
	 */
	class AuthStore extends \yii\db\ActiveRecord
	{
		const STORE_NAME = "store";

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%auth_store}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['group_id', 'pid', 'uid', 'sub_id', 'corp_id', 'manger_id', 'status', 'is_del'], 'integer'],
				[['describe', 'auth_id', 'province', 'city', 'district'], 'string'],
				[['create_time', 'update_time'], 'safe'],
				[['shop_name', 'config_id'], 'string', 'max' => 80],
				[['address', 'qc_url'], 'string', 'max' => 255],
				[['lat', 'lng'], 'string', 'max' => 20],
				[['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
				[['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthStoreGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
				[['manger_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['manger_id' => 'id']],
				[['sub_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubUser::className(), 'targetAttribute' => ['sub_id' => 'sub_id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'group_id'    => Yii::t('app', '分组id'),
				'pid'         => Yii::t('app', '上级店铺'),
				'uid'         => Yii::t('app', '主账户id'),
				'sub_id'      => Yii::t('app', '子账户id'),
				'corp_id'     => Yii::t('app', '企业微信id'),
				'manger_id'   => Yii::t('app', '店长id'),
				'shop_name'   => Yii::t('app', '店铺名称'),
				'describe'    => Yii::t('app', '店铺描述'),
				'status'      => Yii::t('app', '店铺状态'),
				'auth_id'     => Yii::t('app', '店铺权限'),
				'province'    => Yii::t('app', '省'),
				'city'        => Yii::t('app', '市'),
				'district'    => Yii::t('app', '区|县'),
				'address'     => Yii::t('app', '地址'),
				'lat'         => Yii::t('app', '纬度'),
				'lng'         => Yii::t('app', '经度'),
				'qc_url'      => Yii::t('app', '渠道码'),
				'config_id'   => Yii::t('app', '渠道码config'),
				'is_del'      => Yii::t('app', '0未删除1删除'),
				'create_time' => Yii::t('app', '创建时间'),
				'update_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return ActiveQuery
		 */
		public function getCorp ()
		{
			return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getGroup ()
		{
			return $this->hasOne(AuthStoreGroup::className(), ['id' => 'group_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getManger ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'manger_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getSub ()
		{
			return $this->hasOne(SubUser::className(), ['sub_id' => 'sub_id']);
		}

		/**
		 * @return ActiveQuery
		 */
		public function getAuthStoreUsers ()
		{
			return $this->hasMany(AuthStoreUser::className(), ['store_id' => 'id']);
		}

		/**
		 * @return ActiveQuery
		 * @throws InvalidConfigException
		 */
		public function getUsers ()
		{
			return $this->hasMany(WorkUser::className(), ['id' => 'user_id'])->viaTable('{{%auth_store_user}}', ['store_id' => 'id']);
		}

		/**
		 * Title: GiveAddressReturnLngAndLat
		 * User: sym
		 * Date: 2021/1/19
		 * Time: 10:25
		 *
		 * @param $address //地址
		 *
		 * @remark
		 */
		public static function GiveAddressReturnLngAndLat ($address)
		{
			if (empty($address)) {
				return [];
			}
			$key    = \Yii::$app->params['tx_key'];
			$url    = 'https://apis.map.qq.com/ws/geocoder/v1/?address=' . $address . '&key=' . $key;
			$result = SUtils::postUrl($url, []);
			\Yii::error($result, 'tx_address');
			if (!empty($result['result'])) {
				return $result['result'];
			} else {
				return [];
			}
		}

		public static function GiveStoreIdDataReturnAllId (&$storeIds, $corp_id)
		{
			$data = self::find()->where(["corp_id" => $corp_id])->select("id,pid")->orderBy(["pid" => SORT_DESC])->asArray()->all();
			foreach ($data as $record) {
				if (in_array($record["pid"], $storeIds)) {
					$storeIds[] = $record["id"];
				}
			}
			$storeIds = array_unique($storeIds);
		}

	}
