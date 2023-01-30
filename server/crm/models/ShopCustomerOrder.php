<?php

	namespace app\models;

	use app\components\InvalidDataException;
	use app\util\SUtils;
	use dovechen\yii2\weWork\components\HttpUtils;
	use dovechen\yii2\weWork\src\dataStructure\Tag;
	use Imactool\Jinritemai\Shop\Shop;
	use Yii;
	use yii\behaviors\TimestampBehavior;
	use yii\caching\TagDependency;
	use yii\db\Query;

	/**
	 * This is the model class for table "{{%shop_customer_order}}".
	 *
	 * @property int    $id
	 * @property int    $corp_id             授权的企业ID
	 * @property int    $cus_id              顾客ID
	 * @property string $name                顾客姓名
	 * @property int    $source              订单来源:0 手工录入 1小猪电商 2淘宝 3有赞
	 * @property int    $payment_method      支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 5其他第三方支付
	 * @property string $payment_method_name 支付方式名称:微信 建设银行 易宝等
	 * @property string $order_no            唯一订单号
	 * @property float  $payment_amount      订单实际⽀付⾦额
	 * @property int    $guide_id            归属导购员ID
	 * @property string $guide_name          导购姓名
	 * @property int    $other_store_id      第三方店铺id
	 * @property string $other_store_name    第三方店铺名称
	 * @property int    $store_id            ⻔店ID
	 * @property string $store_name          门店信息
	 * @property string $pay_time            ⽀付时间
	 * @property string $buy_name            购买⼈姓名
	 * @property string $buy_phone           购买⼈⼿机号
	 * @property int    $first_buy           是否首次购买:0否1是
	 * @property int    $order_type          订单类型：0正常下单 1拼团 2砍价 等
	 * @property int    $status              订单状态：1正常 2退款
	 * @property float  $refund_amount       订单退金额
	 * @property int    $order_share_id      订单分享明细记录id
	 * @property string $add_time            入库时间
	 * @property string $update_time         更新时间
	 */
	class ShopCustomerOrder extends \yii\db\ActiveRecord
	{
		/**
		 * @var 0 手工录入
		 */
		const SOURCE_PEOPLE = 0;
		/**
		 * @var 1 小猪订单
		 */
		const SOURCE_PIG = 1;
		/**
		 * @var 2 淘宝订单
		 */
		const SOURCE_SHOP = 2;
		/**
		 * @var 3 有赞订单
		 */
		const SOURCE_ZAN = 3;
		/**
		 * @var 4 抖店订单
		 */
		const SOURCE_DOU = 4;

		/**
		 * 支付类型
		 */
		/**
		 * @val 支付宝
		 */
		const PAYMENT_ALIPAY = 1;
		/**
		 * @val 微信支付
		 */
		const PAYMENT_WEIXIN = 2;
		/**
		 * @val 余额支付
		 */
		const PAYMENT_BALANCE = 3;
		/**
		 * @val 银行汇款
		 */
		const PAYMENT_TRANSFER = 4;
		/**
		 * @val 财付通
		 */
		const PAYMENT_TENPAY = 5;
		/**
		 * @val 易宝支付
		 */
		const PAYMENT_YEEPAY = 6;
		/**
		 * @val 通联支付
		 */
		const PAYMENT_ALLINPAY = 7;
		/**
		 * @val 网银在线
		 */
		const PAYMENT_CHINABANK = 8;
		/**
		 * @val 货到付款
		 */
		const PAYMENT_OFFLINE = 9;
		/**
		 * @val 测试支付
		 */
		const PAYMENT_TEST = 10;
		/**
		 * @val 积分抵现
		 */
		const PAYMENT_POINT = 11;
		/**
		 * @val 现金收款
		 */
		const PAYMENT_CASH = 12;
		/**
		 * @val 银行卡收款
		 */
		const PAYMENT_CCB = 13;
		/**
		 * @val 收银宝支付
		 */
		const PAYMENT_VSPALLINPAY = 14;
		/**
		 * @val 拉卡拉支付
		 */
		const PAYMENT_LAKALA = 15;
		/**
		 * @val 渠道红包支付
		 */
		const PAYMENT_CHANNEL = 16;
		/**
		 * @val 其他
		 */
		const PAYMENT_OTHER = 17;
		/**
		 * @val Dou分期
		 */
		const PAYMENT_DOU = 18;
		/**
		 * @val 新卡支付
		 */
		const PAYMENT_NEW = 19;

		/**
		 * @var 0 正常下单
		 */
		const ORDER_NORMAL = 0;
		/**
		 * @var 1 拼团
		 */
		const ORDER_GROUP = 1;
		/**
		 * @var 2 砍价下单
		 */
		const ORDER_BARGAIN = 2;

		/*支付状态*/
		/**
		 * @val 1|正常
		 */
		const STATUS_REAL = 1;
		/**
		 * @val  2|退款
		 */
		const STATUS_REFUND = 2;
		/**
		 * @val  1|首次购买
		 */
		const FIRST_BUY_Y = 1;
		/**
		 * @val  0|非首次购买
		 */
		const FIRST_BUY_N = 0;

		/**
		 * {@inheritdoc}
		 */
		public static function tableName ()
		{
			return '{{%shop_customer_order}}';
		}

		/**
		 * {@inheritdoc}
		 */
		public function rules ()
		{
			return [
				[['corp_id', 'cus_id', 'source', 'payment_method', 'guide_id', 'other_store_id', 'store_id', 'order_type', 'status', 'first_buy', 'order_share_id'], 'integer'],
				[['payment_amount', 'refund_amount'], 'number'],
				[['pay_time', 'add_time', 'update_time'], 'safe'],
				[['name', 'payment_method_name', 'order_no'], 'string', 'max' => 100],
				[['guide_name', 'other_store_name', 'store_name'], 'string', 'max' => 255],
				[['buy_name'], 'string', 'max' => 200],
				[['buy_phone'], 'string', 'max' => 11],
			];
		}

		/**
		 * {@inheritdoc}
		 */
		public function attributeLabels ()
		{
			return [
				'id'                  => Yii::t('app', 'ID'),
				'corp_id'             => Yii::t('app', '授权的企业ID'),
				'cus_id'              => Yii::t('app', '顾客ID'),
				'name'                => Yii::t('app', '顾客姓名'),
				'source'              => Yii::t('app', '订单来源:0 手工录入 1小猪电商 2淘宝 3有赞  '),
				'payment_method'      => Yii::t('app', '支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 5其他第三方支付'),
				'payment_method_name' => Yii::t('app', '支付方式名称:微信 建设银行 易宝等'),
				'order_no'            => Yii::t('app', '唯一订单号'),
				'payment_amount'      => Yii::t('app', '订单实际⽀付⾦额'),
				'refund_amount'       => Yii::t('app', '订单退款⾦额'),
				'guide_id'            => Yii::t('app', '归属导购员ID'),
				'guide_name'          => Yii::t('app', '导购姓名'),
				'other_store_id'      => Yii::t('app', '第三方店铺id'),
				'other_store_name'    => Yii::t('app', '第三方店铺名称'),
				'store_id'            => Yii::t('app', '⻔店ID'),
				'store_name'          => Yii::t('app', '门店信息'),
				'pay_time'            => Yii::t('app', '⽀付时间'),
				'buy_name'            => Yii::t('app', '购买⼈姓名'),
				'buy_phone'           => Yii::t('app', '购买⼈⼿机号'),
				'first_buy'           => Yii::t('app', '首次购买：1是0否'),
				'order_type'          => Yii::t('app', '订单类型：0正常下单 1拼团 2砍价 等'),
				'status'              => Yii::t('app', '订单状态：1正常 2退款'),
				'order_share_id'      => Yii::t('app', 'scrm商品或者页面分享id'),
				'add_time'            => Yii::t('app', '入库时间'),
				'update_time'         => Yii::t('app', '入库时间'),
			];
		}

		/**
		 * 自动添加时间戳，序列化参数
		 * @return array
		 */
		public function behaviors ()
		{
			return [
				[
					'class'      => TimestampBehavior::className(),
					'value'      => date('Y-m-d H:i:s', time()),
					'attributes' => [
						self::EVENT_BEFORE_INSERT => ['add_time'],
					],
				],
			];
		}

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getUser ()
        {
            return $this->hasOne(WorkUser::className(), ['id' => 'guide_id'])->select('id,name');
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getStore ()
        {
            return $this->hasOne(AuthStore::className(), ['id' => 'store_id']);
        }

		public static function getSource ($type = '')
		{
			$source = [
				['id' => 0, 'name' => '手工录入'],
				['id' => 1, 'name' => '电商系统'],
				['id' => 2, 'name' => '淘宝'],
				['id' => 3, 'name' => '有赞'],
				['id' => 4, 'name' => '抖店']
			];
			if (empty($type)) {
				return $source;
			} else if (!empty($source)) {
				$re = [];
				foreach ($source as $v) {
					$re[$v['id']] = $v['name'];
				}

				return $re;
			} else {
				return '';
			}
		}

		public static function getPayType ($type = '')
		{
			$payList = [
				['id' => ShopCustomerOrder::PAYMENT_ALIPAY, 'name' => '支付宝'],
				['id' => ShopCustomerOrder::PAYMENT_WEIXIN, 'name' => '微信支付'],
				['id' => ShopCustomerOrder::PAYMENT_BALANCE, 'name' => '余额支付'],
				['id' => ShopCustomerOrder::PAYMENT_TRANSFER, 'name' => '银行汇款'],
				['id' => ShopCustomerOrder::PAYMENT_TENPAY, 'name' => '财付通'],
				['id' => ShopCustomerOrder::PAYMENT_YEEPAY, 'name' => '易宝支付'],
				['id' => ShopCustomerOrder::PAYMENT_ALLINPAY, 'name' => '通联支付'],
				['id' => ShopCustomerOrder::PAYMENT_CHINABANK, 'name' => '网银在线'],
				['id' => ShopCustomerOrder::PAYMENT_OFFLINE, 'name' => '货到付款'],
				['id' => ShopCustomerOrder::PAYMENT_TEST, 'name' => '测试支付'],
				['id' => ShopCustomerOrder::PAYMENT_POINT, 'name' => '积分抵现'],
				['id' => ShopCustomerOrder::PAYMENT_CASH, 'name' => '现金收款'],
				['id' => ShopCustomerOrder::PAYMENT_CCB, 'name' => '银行卡收款'],
				['id' => ShopCustomerOrder::PAYMENT_VSPALLINPAY, 'name' => '收银宝支付'],
				['id' => ShopCustomerOrder::PAYMENT_LAKALA, 'name' => '拉卡拉支付'],
				['id' => ShopCustomerOrder::PAYMENT_CHANNEL, 'name' => '渠道红包支付'],
				['id' => ShopCustomerOrder::PAYMENT_OTHER, 'name' => '其他'],
				['id' => ShopCustomerOrder::PAYMENT_DOU, 'name' => 'Dou分期'],
				['id' => ShopCustomerOrder::PAYMENT_NEW, 'name' => '新卡支付'],
			];
			if (empty($type)) {
				return $payList;
			} else {
				foreach ($payList as $v) {
					$pay[$v['id']] = $v['name'];
				}

				return $pay;
			}
		}

		//清理订单 数据
		public static function clearOrder ($corpId)
		{
			if (empty($corpId)) {
				return [];
			}
			//先查是否清洗过订单 若清洗过找出最近一次清理订单的时间 增量清洗

			$starData = ShopTaskRecord::getRecord($corpId, ShopTaskRecord::TYPE_ORDER);
			if ($starData != 0) {
				$starData = date('Y-m-d 00:00:00', strtotime($starData));
			}
			//优先级别最高
			$where = ['AND', ['>', 'update_time', $starData], ['corp_id' => $corpId]];
			//第三方电商订单
			$query = (new Query())
				->select('*')
				->from('{{%shop_third_order}}')
				->where($where)
				->orderBy('id');
			foreach ($query->batch() as $qk => $order) {
				if (empty($order)) {
					return true;
				}
				Yii::error('清理订单数据' . $corpId . '数据:' . ($qk * 100 + count($order)), 'clearOrderData');
				self::clearOrderItem($starData, $order);
				unset($users);
			}
			//插入最新更新记录
			ShopTaskRecord::addRecord($corpId, ShopTaskRecord::TYPE_ORDER);
		}

		//清理抖店订单 数据
		public static function clearDouOrder ($corpId)
		{
			if (empty($corpId)) {
				return [];
			}
			//先查是否清洗过订单 若清洗过找出最近一次清理订单的时间 增量清洗
			$starData = ShopTaskRecord::getRecord($corpId, ShopTaskRecord::TYPE_ORDER_DOU);
			//优先级别最高
			$where = ['AND', ['>', 'update_time', $starData], ['corp_id' => $corpId]];
			//第三方电商订单
			$query = (new Query())
				->select('*')
				->from('{{%shop_doudian_order}}')
				->where($where)
				->orderBy('id');
			//对应支付方式转换 0：货到付款，1：微信，2：支付宝,·4：银行卡,5：余额, 8：Dou分期, 9：新卡支付
			$payTypeChange = [0 => 9, 1 => 2, 2 => 1, 4 => 4, 5 => 3, 8 => 18, 9 => 9];
			$payTypeName   = ShopCustomerOrder::getPayType(1);
			foreach ($query->batch() as $qk => $order) {
				if (empty($order)) {
					return true;
				}
				//组合订单清洗的格式
				$orderList = [];
				foreach ($order as $ok => $item) {
					$orderList[] = [
						'corp_id'             => $item['corp_id'],
						'source'              => ShopCustomerOrder::SOURCE_DOU,
						'store_id'            => $item['shop_id'],
						'store_name'          => ShopDoudian::getData($item['corp_id'], $item['shop_id'], 'shop_name'),
						'order_no'            => $item['order_id'],
						'payment_amount'      => $item['order_total_amount'],
						'payment_method'      => $payTypeChange[$item['pay_type']],
						'payment_method_name' => $payTypeName[$payTypeChange[$item['pay_type']]],
						'pay_time'            => $item['pay_time'],
						'buy_name'            => $item['post_receiver'],
						'buy_phone'           => $item['post_tel'],
						'refund_amount'       => $item['refund_amount'],
						'status'              => $item['order_status'] == ShopDoudianOrder::STATUS_REFUND_Y ? ShopCustomerOrder::STATUS_REFUND : ShopCustomerOrder::STATUS_REAL,
						'union_id'            => '',
						'guide_id'            => 0,
						'order_type'          => 0,
					];
					unset($ok);
					unset($item);
				}
				self::clearOrderItem(0, $orderList);
				unset($qk);
				unset($order);
			}
			//插入最新更新记录
			try {
				ShopTaskRecord::addRecord($corpId, ShopTaskRecord::TYPE_ORDER_DOU);
			} catch (InvalidDataException $e) {
				\Yii::error(['插入记录错误=>' => $e], 'clearSeaUser');
			}
		}

		public static function clearOrderItem ($starData, $orderList)
		{
			//清洗订单
			foreach ($orderList as $v) {
				$order['corp_id']   = $v['corp_id'];
				$order['cus_id']    = ShopCustomer::checkCustomer($v);
				$order['first_buy'] = self::checkCustomerFirstBuy($order['cus_id']);
				if (!$order['cus_id']) {
					continue;
				}
				$order['name']                = !empty($v['buy_phone']) ? $v['buy_name'] : $v['receiver_name'];
				$order['name']                = $order['name'] ?: '';
				$order['source']              = isset($v['source']) && !empty($v['source']) ? $v['source'] : ShopCustomerOrder::SOURCE_PIG;
				$order['payment_method']      = $v['payment_method'];
				$order['payment_method_name'] = $v['payment_method_name'];
				$order['payment_amount']      = $v['payment_amount'];
				$order['order_no']            = $v['order_no'];

				//查询导购配置信息
				if (isset($performanceRelatedList[$v['corp_id']]) && isset($isConsumptionList[$v['corp_id']])) {
					$performanceRelated = $performanceRelatedList[$v['corp_id']];
					$isConsumption      = $isConsumptionList[$v['corp_id']];
				} else {
					$config = ShopGuideAttribution::findOne(['corp_id' => $v['corp_id']]);
					if (isset($config) && !empty($config)) {
						$performanceRelated = $performanceRelatedList[$v['corp_id']] = $config->performance_related;
						$isConsumption      = $isConsumptionList[$v['corp_id']] = $config->is_consumption;
					} else {
						$isConsumption = $performanceRelated = '';
					}
				}

				//员工所有导购
				if (!empty($v['guide_id'])) {
					//根据设置的配置信息 业绩归属设置
					/*if (isset($performanceRelated)) {
						$guideId = self::dealOrder($v['guide_id'], $performanceRelated);
						if ($guideId) {*/
							$order['guide_id']   = $v['guide_id'];
							$workUserModel       = WorkUser::findOne(['id' => $v['guide_id']]);
							$order['guide_name'] = (isset($workUserModel) && !empty($workUserModel)) ? $workUserModel->name : '';
						/*}
					}*/
				}
				$order['order_share_id']   = isset($v['order_share_id']) ? $v['order_share_id'] : 0;
				$order['store_id']         = isset($v['scrm_store_id']) ? $v['scrm_store_id'] : 0;
				$order['store_name']       = self::getStoreName($order['store_id']);
				$order['other_store_id']   = isset($v['store_id']) ? $v['store_id'] : 0;
				$order['other_store_name'] = isset($v['store_name']) ? $v['store_name'] : '';
				$order['buy_name']         = isset($v['buy_name']) ? $v['buy_name'] : '';
				$order['buy_phone']        = isset($v['buy_phone']) ? $v['buy_phone'] : '';
				$order['pay_time']         = $v['pay_time'];
				$order['order_type']       = isset($v['order_type']) ? $v['order_type'] : '';
				$order['status']           = isset($v['status']) ? $v['status'] : '';
				$order['refund_amount']    = isset($v['refund_amount']) ? $v['refund_amount'] : '';
				$where                     = [
					'corp_id'  => $order['corp_id'],
					'source'   => $order['source'],
					'order_no' => $order['order_no'],
				];
				self::addOrder($where, $order);

				//绑定导购逻辑
				$sourceType = $starData === 0 ? ShopCustomerGuideRelation::ADD_TYPE_IMPORT : ShopCustomerGuideRelation::ADD_TYPE_SHOPPING;
				if ($isConsumption == ShopGuideAttribution::IS_CONSUMPTION_OPEN && !empty($v['guide_id'])) {
					ShopCustomerGuideRelation::checkStoreGuideRelation($order['corp_id'], $order['cus_id'], $v['guide_id'], $order['store_id'], $sourceType);
				}
				unset($order);
				unset($where);
			}
			unset($orderList);

			return true;
		}

		/**
		 * 业绩归属设置 有订单导入时 处理业绩归属
		 *
		 * @param $guideId 导购id
		 * @param $performanceRelated
		 *
		 * @return int|bool
		 */
		public static function dealOrder ($guideId, $performanceRelated)
		{
			//不关联导购
			if ($performanceRelated == ShopGuideAttribution::RELATION_NO) {
				return 0;
			}
			//关联导购： 导购无门店 或者 订单门店在导购管理门店之中
			if ($performanceRelated == ShopGuideAttribution::RELATION_ONE) {
				return $guideId;
			}

			//默认不关联
			return 0;

		}

		// 获取订单门店信息 TODO:: 方法可移动到 AuthStore 门店控制器
		public static function getStoreName ($storeId)
		{
			$cacheKey = 'auth_store_group_name_' . $storeId;

			return \Yii::$app->cache->getOrSet($cacheKey, function () use ($storeId) {
				$storeMsg = AuthStore::find()->alias('s')
					->leftJoin("{{%auth_store_group}} as g", "s.group_id=g.id")
					->where(['s.id' => $storeId])
					->select('s.shop_name,g.parent_ids,g.name')
					->asArray()
					->one();
				if (!empty($storeMsg['parent_ids'])) {
					$id            = explode(',', $storeMsg['parent_ids']);
					$groupMsg      = AuthStoreGroup::find()
						->where(['status' => 1, 'id' => $id])
						->select('name')->asArray()->all();
					$groupNameList = array_column($groupMsg, 'name');
					$groupName     = implode('-', $groupNameList) . '-' . $storeMsg['shop_name'];
				} else {
					$groupName = $storeMsg['name'] . '-' . $storeMsg['shop_name'];
				}

				return $groupName == '-' ? '' : $groupName;
			}, NULL, new TagDependency(['tags' => ['auth_store', 'auth_store_group']]));
		}

		//获取分组下所有门店id TODO:: 方法可移动到 AuthStoreGroup 门店分组控制器
		public static function getAllGroupData ($uid, $corpId, $id = 0, &$groupStore = [])
		{
			//查询所有分组
			$all = AuthStoreGroup::find()->select('id')
				->where(['uid' => $uid, 'corp_id' => $corpId, 'pid' => $id, 'status' => 1])
				->asArray()
				->all();
			//存在子分组
			if (!empty($all)) {
				foreach ($all as $k => &$child) {
					$child['child'] = AuthStoreGroup::find()
						->where(['uid' => $uid, 'pid' => $child['id'], 'status' => 1])
						->select('id,pid,name')->asArray()->all();
					if (!empty($child['child'])) {
						//$child['child_num'] = count($child['child']);
						$child['child'] = self::getAllGroupData($uid, $corpId, $child['id'], $groupStore);
					} else {
						//查询所有门店
						/*$child['child_num'] = count($child['child']);
						$child['child']     = [];*/
						$store             = AuthStore::find()
							->select('id')
							->where(['uid' => $uid, 'corp_id' => $corpId, 'group_id' => $child['id'], 'status' => 1, 'is_del' => 0])
							->asArray()
							->all();
						$storeId           = array_column($store, 'id');
						$child['store_id'] = array_column($store, 'id');
						$groupStore        = empty($groupStore) ? $storeId : array_merge($groupStore, $storeId);
					}
				}
			} //无子分组
			else {
				$store      = AuthStore::find()
					->select('id')
					->where(['uid' => $uid, 'corp_id' => $corpId, 'group_id' => $id, 'status' => 1, 'is_del' => 0])
					->asArray()
					->all();
				$groupStore = array_column($store, 'id');
			}

			return $groupStore;
		}

		//添加订单 数据
		public static function addOrder ($where, $data)
		{
			$orderModel = ShopCustomerOrder::find()->where($where)->one();

			$oldAttributes = !empty($orderModel) ? clone $orderModel : NULL;
			$orderModel    = !empty($oldAttributes) ? $oldAttributes : new ShopCustomerOrder();
			foreach ($data as $k => $v) {
				//如果修改只更新订单状态退款状态
				if (!empty($oldAttributes) && !in_array($k, ['status', 'refund_amount', 'cus_id'])) {
					unset($data[$k]);
				}
				if (empty($v) && $v !== 0) {
					unset($data[$k]);
				}
			}
			$orderModel->setAttributes($data);
			if (!$orderModel->validate()) {
				throw new InvalidDataException(SUtils::modelError($orderModel));
			}
			!empty($oldAttributes) ? $orderModel->update() : $orderModel->save();

			return $orderModel->id;
		}

		//核对用户是否是首次购买
		public static function checkCustomerFirstBuy ($cusId)
		{
			$re = self::findOne(['cus_id' => $cusId]);

			return empty($re) ? 1 : 0;
		}

		//获取销售额导购数据排行榜
		public static function getRank ($corpId, $startDate, $endDate)
		{
			$cacheKey = 'shop_customer_order_rank_' . $corpId;

			return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId, $startDate, $endDate) {
				//销售额
				$monetaryList = self::find()
					->select('guide_id,sum(payment_amount) as monetary')
					->where(['corp_id' => $corpId])
					->andWhere(['>', 'guide_id', 0])
					->andWhere(['between', 'pay_time', $startDate, $endDate])
					->groupBy('guide_id')
					->orderBy('monetary desc')
					->limit(10)
					->asArray()
					->all();
				//拉新
				$addUserList = self::find()
					->select('guide_id,count(distinct case when first_buy=1 then cus_id else null end) as add_user_number')
					->where(['corp_id' => $corpId])
					->andWhere(['>', 'guide_id', 0])
					->andWhere(['between', 'pay_time', $startDate, $endDate])
					->groupBy('guide_id')
					->orderBy('add_user_number desc')
					->limit(10)
					->asArray()
					->all();
				$guideIds    = $customerGuideIds = $customerNum = [];
				if (!empty($monetaryList)) {
					foreach ($monetaryList as $mv) {
						$guideIds[] = $mv['guide_id'];
					}
				}
				if (!empty($addUserList)) {
					foreach ($addUserList as $av) {
						$guideIds[]         = $av['guide_id'];
						$customerGuideIds[] = $av['guide_id'];
					}
				}
				if (!empty($customerGuideIds)) {
					$customerList = ShopCustomerGuideRelation::find()
						->select('guide_id,count(cus_id) all_num')
						->where(['guide_id' => $customerGuideIds, 'corp_id' => $corpId, 'status' => 1])
						->groupBy('guide_id')
						->asArray()
						->all();
					if (!empty($customerList)) {
						foreach ($customerList as $cv) {
							$customerNum[$cv['guide_id']] = $cv['all_num'];
						}
					}
				}
				//查询所有导购名称
				if (!empty($guideIds)) {
					$guideName = [];
					$guideList = WorkUser::find()->where(['id' => $guideIds])->select('name,id')->asArray()->all();
					foreach ($guideList as $gl) {
						$guideName[$gl['id']] = $gl['name'];
					}
					foreach ($monetaryList as $k => &$mv) {
						$mv['key']        = $k + 1;
						$mv['guide_name'] = isset($guideName[$mv['guide_id']]) ? $guideName[$mv['guide_id']] : '-';
					}
					foreach ($addUserList as $k => &$av) {
						$av['key']                 = $k + 1;
						$av['guide_name']          = isset($guideName[$av['guide_id']]) ? $guideName[$av['guide_id']] : '-';
						$av['all_customer_number'] = isset($customerNum[$av['guide_id']]) ? $customerNum[$av['guide_id']] : 0;

					}
				}

				return ['monetary_list' => $monetaryList, 'add_user_list' => $addUserList];
			}, NULL, new TagDependency(['tags' => ['shop_customer_guide_relation_' . $corpId, 'shop_customer_order_' . $corpId, 'shop_customer_order', 'shop_customer_guide_relation']]));
		}

	}
