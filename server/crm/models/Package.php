<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use Yii;
	use app\util\DateUtil;

	/**
	 * This is the model class for table "{{%package}}".
	 *
	 * @property int              $id
	 * @property string           $name                  套餐名称
	 * @property double           $old_price             原价
	 * @property double           $price                 现价
	 * @property int              $wechat_num            企业微信数量
	 * @property int              $account_num           公众号数量
	 * @property int              $message_num           消息配额
	 * @property int              $sub_account_num       子账户数量
	 * @property int              $is_trial              是否是试用
	 * @property int              $is_agent              代理商是否可用1是0否
	 * @property int              $sort                  套餐等级排序
	 * @property string           $priceJson             套餐档位价格
	 * @property int              $status                状态：1、开启，2、禁用，3、删除
	 * @property string           $update_time           更新时间
	 * @property string           $create_time           创建时间
	 * @property int              $market_config_is_open 是否开启营销引流客户添加数量限制的开关
	 * @property int              $fission_num           裂变引流的客户上限数
	 * @property int              $lottery_draw_num      抽奖引流的客户上限数
	 * @property int              $red_envelopes_num     红包裂变的客户上限数
	 * @property int              $tech_img_show         底部版权是否展示
	 * @property int              $follow_num            单个渠道活码添加上线
	 * @property int              $follow_open           渠道活码限制开启关闭
	 *
	 * @property DefaultPackage[] $defaultPackages
	 * @property PackageMenu[]    $packageMenus
	 * @property UserPackage[]    $userPackages
	 */
	class Package extends \yii\db\ActiveRecord
	{
		const FREE_PACKAGE_TIME_NUM = 7;//免费套餐时长
		const FREE_PACKAGE_TIME_TYPE = 1;//免费套餐时间类型(日)

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%package}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['old_price', 'price'], 'number'],
				[['follow_open', 'follow_num', 'is_trial', 'status', 'message_num', 'sub_account_num', 'wechat_num', 'account_num', 'is_agent', 'sort'], 'integer'],
				[['update_time', 'create_time'], 'safe'],
				[['name'], 'string', 'max' => 32],
				[['priceJson'], 'string'],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                    => Yii::t('app', 'ID'),
				'name'                  => Yii::t('app', '套餐名称'),
				'old_price'             => Yii::t('app', '原价'),
				'price'                 => Yii::t('app', '现价'),
				'wechat_num'            => Yii::t('app', '企业微信数量'),
				'account_num'           => Yii::t('app', '公众号数量'),
				'message_num'           => Yii::t('app', '消息配额'),
				'sub_account_num'       => Yii::t('app', '子账户数量'),
				'is_trial'              => Yii::t('app', '是否是试用'),
				'is_agent'              => Yii::t('app', '代理商是否可用1是0否'),
				'sort'                  => Yii::t('app', '套餐等级排序'),
				'priceJson'             => Yii::t('app', '套餐档位价格'),
				'status'                => Yii::t('app', '状态：1、开启，2、禁用，3、删除'),
				'update_time'           => Yii::t('app', '更新时间'),
				'create_time'           => Yii::t('app', '创建时间'),
				'market_config_is_open' => Yii::t('app', '是否开启营销引流客户添加数量限制的开关'),
				'fission_num'           => Yii::t('app', '裂变引流的客户上限数'),
				'lottery_draw_num'      => Yii::t('app', '抽奖引流的客户上限数'),
				'red_envelopes_num'     => Yii::t('app', '红包裂变的客户上限数'),
				'tech_img_show'         => Yii::t('app', '底部版权是否展示'),
				'follow_open'           => Yii::t('app', '渠道活码限制开启关闭'),
				'follow_num'            => Yii::t('app', '单个渠道活码添加上线'),
			];
		}

		/**
		 * @return object|\yii\db\Connection|null
		 *
		 * @throws \yii\base\InvalidConfigException
		 */
		public static function getDb ()
		{
			return Yii::$app->get('mdb');
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getDefaultPackages ()
		{
			return $this->hasMany(DefaultPackage::className(), ['package_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getPackageMenus ()
		{
			return $this->hasMany(PackageMenu::className(), ['package_id' => 'id']);
		}

		/**
		 * @return \yii\db\ActiveQuery
		 */
		public function getUserPackages ()
		{
			return $this->hasMany(UserPackage::className(), ['package_id' => 'id']);
		}

		//套餐详情
		public static function dumpData ($packageId, $packageData = '')
		{
			$result = [];

			if (empty($packageData)) {
				$packageData = static::findOne($packageId);
			}
			if (!empty($packageData)) {
				$result = [
					'id'                    => $packageData['id'],
					'name'                  => $packageData['name'],
					'old_price'             => $packageData['old_price'],
					'price'                 => $packageData['price'],
					'wechat_num'            => $packageData['wechat_num'],
					'account_num'           => $packageData['account_num'],
					'message_num'           => $packageData['message_num'],
					'sub_account_num'       => $packageData['sub_account_num'],
					'is_trial'              => $packageData['is_trial'],
					'is_agent'              => $packageData['is_agent'],
					'sort'                  => $packageData['sort'],
					'priceJson'             => $packageData['priceJson'],
					'status'                => $packageData['status'],
					'authority'             => PackageMenu::dumpData($packageId),
					'market_config_is_open' => $packageData['market_config_is_open'],
					'fission_num'           => $packageData['fission_num'],
					'lottery_draw_num'      => $packageData['lottery_draw_num'],
					'red_envelopes_num'     => $packageData['red_envelopes_num'],
					'tech_img_show'         => $packageData['tech_img_show'],
					'follow_num'            => $packageData['follow_num'],
					'follow_open'           => $packageData['follow_open'],
				];
			}

			return $result;
		}

		//获取全部套餐
		public static function getAllPackageInfo ($isAgent = 0)
		{
			$result      = [];
			$packageData = static::find()->where(['status' => 1]);
			if ($isAgent) {
				$packageData = $packageData->andWhere(['is_agent' => 1]);
			}
			$packageData = $packageData->orderBy(['sort' => SORT_ASC])->all();

			if (empty($packageData)) {
				//添加免费套餐
				static::setFreePackage();

				$packageData = static::find()->where(['status' => 1])->all();
			}

			if (!empty($packageData)) {
				foreach ($packageData as $package) {
					$packageInfo = static::dumpData($package['id'], $package);

					array_push($result, $packageInfo);
				}
			}

			return $result;
		}

		/**
		 * 设置免费套餐
		 */
		public static function setFreePackage ()
		{
			$data                   = [];
			$data['name']           = '免费套餐';
			$data['is_trial']       = 1;
			$data['is_agent']       = 1;
			$data['status']         = 1;
			$data['create_time']    = DateUtil::getCurrentTime();
			$priceD                 = [];
			$priceD['timeType']     = static::FREE_PACKAGE_TIME_TYPE;
			$priceD['timeNum']      = static::FREE_PACKAGE_TIME_NUM;
			$priceD['sendTimeType'] = 0;
			$priceD['sendTimeNum']  = 0;
			$priceD['nowPrice']     = 0;
			$priceData[]            = $priceD;
			$data['priceJson']      = json_encode($priceData);
			//默认全部权限
			$topMenu   = Menu::find()->where(['level' => 1, 'status' => Menu::SHOW_MENU])->all();
			$authority = [];
			foreach ($topMenu as $v) {
				array_push($authority, $v['id']);
			}
			$data['authority'] = $authority;

			static::setPackage($data);

			return true;
		}

		//设置套餐
		public static function setPackage ($data)
		{
			$packageId         = !empty($data['id']) ? $data['id'] : 0;
			$name              = !empty($data['name']) ? $data['name'] : '';
			$old_price         = !empty($data['old_price']) ? $data['old_price'] : 0;
			$wechat_num        = !empty($data['wechat_num']) ? $data['wechat_num'] : 0;
			$account_num       = !empty($data['account_num']) ? $data['account_num'] : 0;
			$message_num       = !empty($data['message_num']) ? $data['message_num'] : 0;
			$sub_account_num   = !empty($data['sub_account_num']) ? $data['sub_account_num'] : 0;
			$price             = !empty($data['price']) ? $data['price'] : 0;
			$authority         = !empty($data['authority']) ? $data['authority'] : [];
			$menuLimit         = !empty($data['menuLimit']) ? $data['menuLimit'] : [];//功能限制数量
			$priceJson         = !empty($data['priceJson']) ? $data['priceJson'] : '';
			$is_agent          = !empty($data['is_agent']) ? $data['is_agent'] : 0;
			$sort              = !empty($data['sort']) ? $data['sort'] : 0;
			$is_trial          = !empty($data['is_trial']) ? 1 : 0;
			$transaction       = \Yii::$app->db->beginTransaction();
			$market_config     = !empty($data['market_config_is_open']) ? $data['market_config_is_open'] : 0;
			$fission_num       = !empty($data['fission_num']) && $market_config ? $data['fission_num'] : 0;
			$lottery_draw_num  = !empty($data['lottery_draw_num']) && $market_config ? $data['lottery_draw_num'] : 0;
			$red_envelopes_num = !empty($data['red_envelopes_num']) && $market_config ? $data['red_envelopes_num'] : 0;
			$tech_img_show     = !empty($data['tech_img_show']) ? $data['tech_img_show'] : 0;
			$follow_num        = !empty($data['follow_num']) ? $data['follow_num'] : 0;
			$follow_open       = !empty($data['follow_open']) ? $data['follow_open'] : 0;
			try {
				$nameInfo = static::find()->where(['name' => $name, 'status' => [1, 2]]);
				if (!empty($packageId)) {
					$package              = Package::findOne($packageId);
					$nameInfo             = $nameInfo->andWhere(['<>', 'id', $packageId]);
					$package->update_time = DateUtil::getCurrentTime();
				} else {
					$package              = new Package();
					$package->create_time = DateUtil::getCurrentTime();
					$package->is_trial    = $is_trial;
				}
				$nameInfo = $nameInfo->one();
				if (!empty($nameInfo)) {
					throw new InvalidDataException('套餐名称已经存在');
				}

				$package->name                  = $name;
				$package->is_agent              = $is_agent;
				$package->sort                  = $sort;
				$package->old_price             = $old_price;
				$package->price                 = $price;
				$package->wechat_num            = $wechat_num;
				$package->account_num           = $account_num;
				$package->message_num           = $message_num;
				$package->sub_account_num       = $sub_account_num;
				$package->status                = 1;
				$package->priceJson             = $priceJson;
				$package->market_config_is_open = $market_config;
				$package->fission_num           = $fission_num;
				$package->lottery_draw_num      = $lottery_draw_num;
				$package->red_envelopes_num     = $red_envelopes_num;
				$package->tech_img_show         = $tech_img_show;
				$package->follow_num            = $follow_num;
				$package->follow_open           = $follow_open;
				if (!$package->save()) {
					throw new InvalidDataException(SUtils::modelError($package));
				}
				$packageId = $package->id;
				//添加对应关系
				PackageMenu::setAuthority($packageId, $authority, $menuLimit);

				$transaction->commit();
			} catch (InvalidDataException $e) {
				$transaction->rollBack();
				throw new InvalidDataException($e->getMessage());
			}

			return ['error' => 0, 'msg' => ''];
		}

		/**
		 * 获取用户默认套餐
		 *
		 */
		public static function getDefaultPackage ()
		{
			$defaultPackage = DefaultPackage::find()->one();

			if (empty($defaultPackage)) {
				//免费套餐
				$freePackage = static::findOne(['is_trial' => 1]);

				if (empty($freePackage)) {
					//添加免费套餐
					static::setFreePackage();

					$freePackage = static::findOne(['is_trial' => 1]);
				}

				$packagePrice = json_decode($freePackage->priceJson, true);

				$defaultPackage                    = new DefaultPackage();
				$defaultPackage->package_id        = $freePackage->id;
				$defaultPackage->duration          = $packagePrice[0]['timeNum'];
				$defaultPackage->duration_type     = $packagePrice[0]['timeType'];
				$defaultPackage->expire_type       = 1;
				$defaultPackage->expire_package_id = 0;
				$defaultPackage->time              = time();

				if (!$defaultPackage->save()) {
					throw new InvalidDataException(SUtils::modelError($defaultPackage));
				}
			}

			return $defaultPackage;
		}

		/**
		 * 获取用户当前套餐 并更新登录时间
		 *
		 */
		public static function getUserPackage ($uid)
		{
			$user = User::findOne($uid);

			if (!empty($user->package_id)) {
				if ($user->end_time < time()) {
					//套餐到期处理
					$defaultPackage = static::getDefaultPackage();

					if ($defaultPackage->expire_type == 1) {
						return ['error' => 1, 'msg' => '帐号套餐已到期，无法登录'];
					}
				}
			} else {
				//默认套餐
				$defaultPackage = static::getDefaultPackage();

				$user->package_id   = $defaultPackage->package_id;
				$user->package_time = $defaultPackage->duration;
				$user->time_type    = $defaultPackage->duration_type;
				if ($user->time_type == 2) {
					$time_type = 'month';
				} elseif ($user->time_type == 3) {
					$time_type = 'year';
				} else {
					$time_type = 'day';
				}
				$end_time = strtotime("$user->create_time +$user->package_time $time_type");
				if ($end_time % 86400 == 0) {
					$user->end_time = $end_time;
				} else {
					$user->end_time = strtotime(date('Y-m-d', $end_time)) + 86399;
				}

				if (!$user->save()) {
					throw new InvalidDataException(SUtils::modelError($user));
				}

				if ($user->end_time < time() && $defaultPackage->expire_type == 1) {
					return ['error' => 1, 'msg' => '帐号套餐已到期，无法登录'];
				}
			}

			//更新登录时间
			$user->login_time = time();
			if (!$user->save()) {
				throw new InvalidDataException(SUtils::modelError($user));
			}

			return ['error' => 0, 'msg' => ''];
		}

		/**
		 * @title 获取套餐限制
		 *
		 * @param $uid
		 * @param $type 功能类型
		 *
		 * @return mixed|string
		 *
		 */
		public static function packageLimitNum ($uid, $type)
		{
			$userPackage = UserPackage::findOne(['user_id' => $uid]);

			$limitNum = '';
			if ($userPackage['package_id']) {
				if (in_array($type, ['wechat_num', 'account_num', 'message_num', 'sub_account_num'])) {
					//企业微信数量 公众号数量 消息配额 子账户数量
					$packageInfo = Package::findOne($userPackage['package_id']);
					$limitNum    = $packageInfo[$type];
				} else {
					$packageMenu = [];
					switch ($type) {
						case 'channelCode'://渠道活码数量
							$packageMenu = PackageMenu::findOne(['package_id' => $userPackage['package_id'], 'menu_id' => 28]);
							break;
						case 'fans'://粉丝数量
							$packageMenu = PackageMenu::findOne(['package_id' => $userPackage['package_id'], 'menu_id' => 13]);
							break;
						case 'scene'://渠道二维码数量
							$packageMenu = PackageMenu::findOne(['package_id' => $userPackage['package_id'], 'menu_id' => 8]);
							break;
						case 'filingCabinet'://内容引擎存储空间
							$packageMenu = PackageMenu::findOne(['package_id' => $userPackage['package_id'], 'menu_id' => 31]);
							break;

					}
					$limitNum = isset($packageMenu['use_limit']) && $packageMenu['use_limit'] > 0 ? $packageMenu['use_limit'] : '';
				}
			}

			return $limitNum;
		}
	}
