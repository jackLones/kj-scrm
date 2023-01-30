<?php

	namespace app\models;

	use Yii;

	/**
	 * This is the model class for table "{{%auth_store_user}}".
	 *
	 * @property int       $id
	 * @property int       $store_id    店铺id
	 * @property int       $user_id     员工id
	 * @property int       $status      1正常0取消
	 * @property string    $qc_url      渠道码
	 * @property string    $config_id   渠道码config
	 * @property string    $create_time 创建时间
	 *
	 * @property AuthStore $store
	 * @property WorkUser  $user
	 */
	class AuthStoreUser extends \yii\db\ActiveRecord
	{
		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%auth_store_user}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['store_id', 'user_id', 'status'], 'integer'],
				[['create_time'], 'safe'],
				[['qc_url'], 'string', 'max' => 255],
				[['config_id'], 'string', 'max' => 80],
				[['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => AuthStore::className(), 'targetAttribute' => ['store_id' => 'id']],
				[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'          => Yii::t('app', 'ID'),
				'store_id'    => Yii::t('app', '店铺id'),
				'user_id'     => Yii::t('app', '员工id'),
				'status'      => Yii::t('app', '1正常0取消'),
				'qc_url'      => Yii::t('app', '渠道码'),
				'config_id'   => Yii::t('app', '渠道码config'),
				'create_time' => Yii::t('app', '创建时间'),
			];
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getStore ()
		{
			return $this->hasOne(AuthStore::className(), ['id' => 'store_id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUser ()
		{
			return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
		}

		/**
		 * Title: GetStoreUserData
		 * User: sym
		 * Date: 2021/1/19
		 * Time: 11:22
		 *
		 * @param $storeUserIds
		 * @param $corp_id
		 *
		 * @remark 获取门店下的员工增长数据
		 */
		public static function GetStoreUserData ($storeUserIds, $corp_id)
		{

			/**获取店铺员工*/
			$StoreUserTemp = AuthStoreUser::find()->alias("a")
				->leftJoin("{{%work_user}} as b", "a.user_id = b.id")
				->where(["and", ["b.corp_id" => $corp_id], ["in", "user_id", $storeUserIds]])
				->select("a.user_id,a.store_id")->asArray()->all();
			$storeIds      = array_column($StoreUserTemp, "store_id");
			/**获取店铺员工历史数据*/
			$StoreUserTemp2         = WorkExternalContactFollowUser::find()->alias("a")
				->leftJoin("{{%work_user}} as b", "a.user_id = b.id")
				->where(["and", ["b.corp_id" => $corp_id], ["in", "a.user_id", $storeUserIds]])
				->andWhere(["in", "store_id", $storeIds])
				->select("a.createtime,a.del_type,a.user_id")->asArray()->all();
			$newDataFollowAll       = [];
			$newDataFollowNow       = [];
			$newDataFollowDELALL    = [];
			$newDataFollowDELALLNow = [];

			$newDataStoreALL       = [];
			$newDataStoreNow       = [];
			$newDataStoreDelALLNow = [];
			$newDataStoreDelNow    = [];
			/**格式化历史数据*/
			foreach ($StoreUserTemp2 as $dd) {
				$newDataFollowAll[$dd["user_id"]][] = $dd;
				if ($dd["del_type"] != 0) {
					$newDataFollowDELALL[$dd["user_id"]][] = $dd;
				}
				if ($dd["createtime"] > strtotime(date("Y-m-d", time()))) {
					$newDataFollowNow[$dd["user_id"]][] = $dd;
					if ($dd["del_type"] != 0) {
						$newDataFollowDELALLNow[$dd["user_id"]][] = $dd;
					}
				}

			}
			foreach ($StoreUserTemp as $vv) {
				if (isset($newDataFollowAll[$vv["user_id"]])) {
					++$newDataStoreALL[$vv["store_id"]];
				} else {
					$newDataStoreALL[$vv["store_id"]] = 0;
				}
				if (isset($newDataFollowNow[$vv["user_id"]])) {
					++$newDataStoreNow[$vv["store_id"]];
				} else {
					$newDataStoreNow[$vv["store_id"]] = 0;
				}
				if (isset($newDataFollowDELALL[$vv["user_id"]])) {
					++$newDataStoreDelALLNow[$vv["store_id"]];
				} else {
					$newDataStoreDelALLNow[$vv["store_id"]] = 0;
				}
				if (isset($newDataFollowDELALLNow[$vv["user_id"]])) {
					++$newDataStoreDelNow[$vv["store_id"]];
				} else {
					$newDataStoreDelNow[$vv["store_id"]] = 0;
				}
			}

			return [$newDataStoreALL, $newDataStoreNow, $newDataStoreDelALLNow, $newDataStoreDelNow];
		}

	}
