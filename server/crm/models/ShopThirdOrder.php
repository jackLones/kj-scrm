<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use dovechen\yii2\weWork\components\HttpUtils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%shop_third_order}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property string $shop_api_key 对接的key, 冗余
 * @property int $source 订单来源:0 手工录入 1小猪电商 2淘宝 3有赞
 * @property string $order_no 唯一订单号
 * @property float $payment_amount 订单实际⽀付⾦额
 * @property float $post_fee 邮费
 * @property int $use_points 积分
 * @property float $adjust_fee 手工调整金额
 * @property string $send_time 发货时间
 * @property string $end_time 订单完成时间
 * @property int $payment_method 支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 5其他第三方支付
 * @property string $payment_method_name 支付方式名称:微信 建设银行 易宝等
 * @property int $guide_id 归属导购员ID
 * @property int $store_id 第三方系统的⻔店ID
 * @property int $store_name 第三方系统的店铺名称
 * @property int $scrm_store_id scrm 门店ID
 * @property string $pay_time ⽀付时间
 * @property string $buy_name 购买⼈姓名
 * @property string $buy_phone 购买⼈⼿机号
 * @property string $receiver_name 收货⼈姓名
 * @property string $receiver_phone 收货⼈⼿机号
 * @property string $receiver_zip 收件人邮编，非必填
 * @property string $receiver_state 收件人省份
 * @property string $receiver_city 收件人城市
 * @property string $receiver_district 收件人区县
 * @property string $receiver_town 收件人街道，非必填
 * @property string $receiver_address 详细地址，不包含省市区的地址
 * @property string $union_id 用户union_id
 * @property int $status 支付状态: 1未支付 2未发货 3已发货 4已完成 5已取消 6退款中 7确认收货
 * @property float $refund_amount 订单退金额
 * @property string $ext_field 第三方自定义字段
 * @property int $order_type 订单类型 0:普通,1:代付,2:送礼,3:分销,4:活动,5:批发,6:拼团,7:预售,10:预约,11:选购,50:砍价,51:人气夺宝,53:秒杀,55:降价拍,56:抽奖,57:摇一摇,58:微聚力,59:拆礼盒,61:集字游戏,62:摇钱树游戏,63:竞价,64:扫码,65:限时折扣,
 * @property string $order_share_id scrm商品或者页面分享id
 * @property string $add_time 入库时间
 * @property string $update_time 更新时间
 */
class ShopThirdOrder extends \yii\db\ActiveRecord
{
    /**
     * @var 0 手工录入
     */
    const SOURCE_HAND = 0;
    /**
     * @var 1 小猪电商
     */
    const SOURCE_PIG = 1;

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
     * @val  1|拉取订单任务已经加入队列正等待执行
     */
    const PUSH_ORDER_STATUS_WAITE = 1;

    /**
     * @val  2|拉取订单任务已经开始执行
     */
    const PUSH_ORDER_STATUS_START = 2;

    /**
     * @val  3|拉取订单任务已经执行完成
     */
    const PUSH_ORDER_STATUS_FINISH = 3;

    /**
     * @val  -2|拉取订单任务失败！
     */
    const PUSH_ORDER_STATUS_ERROR = -2;

    /**
     * @val  拉取订单状态缓存键名
     */
    const PUSH_ORDER_STATUS_KEY = 'pig_order_push_status';
    /**
     * @val  拉取订单状态提示信息键名
     */
    const PUSH_ORDER_STATUS_MSG = 'pig_order_push_msg';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_third_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'source', 'payment_method', 'guide_id', 'store_id', 'scrm_store_id', 'status', 'order_type', 'order_share_id'], 'integer'],
            [['payment_amount', 'post_fee', 'adjust_fee', 'use_points', 'refund_amount'], 'number'],
            [['send_time', 'end_time', 'pay_time', 'add_time', 'update_time'], 'safe'],
            [['ext_field'], 'required'],
            [['ext_field'], 'string'],
            [['shop_api_key', 'order_no', 'payment_method_name', 'receiver_zip', 'receiver_state', 'receiver_city', 'receiver_district', 'receiver_town'], 'string', 'max' => 100],
            [['buy_name', 'receiver_name'], 'string', 'max' => 150],
            [['buy_phone', 'receiver_phone'], 'string', 'max' => 20],
            [['receiver_address', 'union_id', 'store_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                  => Yii::t('app', 'ID'),
            'corp_id'             => Yii::t('app', '授权的企业ID'),
            'shop_api_key'        => Yii::t('app', '对接的key, 冗余'),
            'source'              => Yii::t('app', '订单来源:0 手工录入 1小猪电商 2淘宝 3有赞 '),
            'order_no'            => Yii::t('app', '唯一订单号'),
            'payment_amount'      => Yii::t('app', '订单实际⽀付⾦额'),
            'refund_amount'       => Yii::t('app', '订单退款⾦额'),
            'post_fee'            => Yii::t('app', '邮费'),
            'use_points'          => Yii::t('app', '积分'),
            'adjust_fee'          => Yii::t('app', '手工调整金额'),
            'send_time'           => Yii::t('app', '发货时间'),
            'end_time'            => Yii::t('app', '订单完成时间'),
            'payment_method'      => Yii::t('app', '支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 5其他第三方支付'),
            'payment_method_name' => Yii::t('app', '支付方式名称:微信 建设银行 易宝等'),
            'guide_id'            => Yii::t('app', '归属导购员ID'),
            'store_id'            => Yii::t('app', '第三方系统的⻔店ID'),
            'store_name'          => Yii::t('app', '第三方系统的店铺名称'),
            'scrm_store_id'       => Yii::t('app', 'scrm 门店ID'),
            'pay_time'            => Yii::t('app', '⽀付时间'),
            'buy_name'            => Yii::t('app', '购买⼈姓名'),
            'buy_phone'           => Yii::t('app', '购买⼈⼿机号'),
            'receiver_name'       => Yii::t('app', '收货⼈姓名'),
            'receiver_phone'      => Yii::t('app', '收货⼈⼿机号'),
            'receiver_zip'        => Yii::t('app', '收件人邮编，非必填'),
            'receiver_state'      => Yii::t('app', '收件人省份'),
            'receiver_city'       => Yii::t('app', '收件人城市'),
            'receiver_district'   => Yii::t('app', '收件人区县'),
            'receiver_town'       => Yii::t('app', '收件人街道，非必填'),
            'receiver_address'    => Yii::t('app', '详细地址，不包含省市区的地址'),
            'union_id'            => Yii::t('app', '用户union_id'),
            'status'              => Yii::t('app', '支付状态: 1正常 2退款'),
            'ext_field'           => Yii::t('app', '第三方自定义字段'),
            'order_type'          => Yii::t('app', '订单类型 0:普通,1:代付,2:送礼,3:分销,4:活动,5:批发,6:拼团,7:预售,10:预约,11:选购,50:砍价,51:人气夺宝,53:秒杀,55:降价拍,56:抽奖,57:摇一摇,58:微聚力,59:拆礼盒,61:集字游戏,62:摇钱树游戏,63:竞价,64:扫码,65:限时折扣,'),
            'add_time'            => Yii::t('app', '入库时间'),
            'order_share_id'      => Yii::t('app', 'scrm商品或者页面分享id'),
            'update_time'         => Yii::t('app', '更新时间'),
        ];
    }

    //关联产品表
    public function getProduct()
    {
        return $this->hasMany(ShopThirdOrderProduct::className(), ['third_order_id' => 'id']);
    }

    //关联产品表
    public function getCoupon()
    {
        return $this->hasMany(ShopThirdOrderCoupon::className(), ['third_order_id' => 'id']);
    }


    //关联订单分享人
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'guide_id']);
    }

    //循环添加订单
    public static function addOrderArray($corpId, $config, $page = 1, $pageSize = 100, $day = 60)
    {
        $response = self::getThirdOrder($config, $page, $pageSize, $day);
        //处理拉取的订单数据
        if ($response['code'] == 0) {
            $order   = $response['data'];
            $count   = $response['total_num'];
            $allPage = ceil($count / $pageSize);
            if ($count == 0) {
                \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $corpId, ShopThirdOrder::PUSH_ORDER_STATUS_FINISH);
                \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $corpId, '第三方近60天订单为空');
                return true;
            }
        } else {
            \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $corpId, ShopThirdOrder::PUSH_ORDER_STATUS_ERROR);
            \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $corpId, $response['message']);
            return $response;
        }

        //添加本页数据
        foreach ($order as $item) {
            self::addOrderOne($corpId, $config['shop_api_key'], $item);
        }

        //循环请求分页数据
        if ($page < $allPage) {
            self::addOrderArray($corpId, $config, $page + 1, $pageSize, $day);
        }
        //修改为执行完成状态
        \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $corpId, ShopThirdOrder::PUSH_ORDER_STATUS_FINISH);
        \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $corpId, '拉取订单任务已经完成！');
        return ['result' => $count];
    }

    //获取第三方信息
    public static function getThirdOrder($config, $page = 1, $pageSize = 100, $day = 60)
    {
        $postUrl    = $config['order_pull_url'];
        $postKey    = $config['third_api_key'];
        $postSecret = $config['third_api_secret'];
        $params     = [
            'app_key'     => $postKey,
            'method'      => 'order_list',
            'timestamp'   => date('Y-m-d H:i:s', time()),
            'format'      => 'json',
            'version'     => '1.0',
            'sign_method' => 'md5',
            'page'        => $page,
            'page_size'   => $pageSize,//每页数量
            'day'         => $day,//初始化最近订单天数
        ];
        //生成签名
        try {
            $params['sign'] = self::sign($params, $postSecret);
            $postData       = HttpUtils::Array2Json($params);
            $responseStr    = HttpUtils::httpPost($postUrl, $postData);
            $re             = ArrayHelper::toArray(json_decode($responseStr));
            if (isset($re['code']) && $re['code'] > 0) {
                $re['message'] = '请求成功，返回错误信息：' . $re['message'];
            }
        } catch (\Exception $e) {
            $re = [
                'code'    => -1,
                'message' => '请求失败：' . $e->getMessage() . ',请核对拉取地址!'
            ];
        }
        return $re;
    }

    //添加单条订单
    public static function addOrderOne($corpId, $shopApiKey, $thirdOrder)
    {

        //产品
        $products = [];
        if (isset($thirdOrder['product']) && !empty($thirdOrder['product'])) {
            $products = $thirdOrder['product'];
            unset($thirdOrder['product']);
        }
        //优惠券
        $coupons = [];
        if (isset($thirdOrder['coupon']) && !empty($thirdOrder['coupon'])) {
            $coupons = $thirdOrder['coupon'];
            unset($thirdOrder['coupon']);
        }

        $where['shop_api_key'] = $shopApiKey;
        $where['order_no']     = $thirdOrder['order_no'];

        //如果存在订单分享id则查找导购id
        if (!empty($thirdOrder['order_share_id'])) {
            $shareData = ShopMaterialSourceRelationship::findOne($thirdOrder['order_share_id']);
            if (!empty($shareData)) {
                $thirdOrder['guide_id'] = $shareData['user_id'];
            }
        }
        if (!empty($thirdOrder['order_share_id'])) {
            $shareOrder = ShopMaterialSourceRelationship::findOne(['id' => $thirdOrder['order_share_id']]);
            if (empty($shareOrder) || (array_key_exists('material_type', $shareOrder) && $shareOrder['material_type'] == ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON)) {
                $thirdOrder['order_share_id'] = 0;
            }
        }
        //保存订单
        $thirdOrder['corp_id']      = $corpId;
        $thirdOrder['shop_api_key'] = $shopApiKey;
        $thirdOrder['source']       = ShopThirdOrder::SOURCE_PIG;
        $thirdOrderId               = ShopThirdOrder::addThirdOrder($where, $thirdOrder);

        if (!empty($thirdOrderId)) {
            //保存订单对应的产品
            if (!empty($products)) {
                foreach ($products as $product) {
                    $product_where             = [
                        'third_order_id' => $thirdOrderId,
                        'product_id'     => $product['product_id'],
                        'sku_id'         => $product['sku_id'],
                    ];
                    $product['third_order_id'] = $thirdOrderId;
                    ShopThirdOrderProduct::addThirdOrderProduct($product_where, $product);
                }
            }
            //添加顾客
            ShopCustomer::checkCustomer($thirdOrder);
            //保存对应的优惠券
            if (!empty($coupons)) {
                foreach ($coupons as $coupon) {
                    $couponWhere              = [
                        'third_order_id' => $thirdOrderId,
                        'coupon_id'      => $coupon['coupon_id']
                    ];
                    $coupon['third_order_id'] = $thirdOrderId;
                    $coupon['corp_id']        = $corpId;
                    //如果存在分享券id 检验当前券是否是分享的券
                    $couponShareId = 0;
                    if (!empty($coupon['coupon_share_id'])) {
                        $shareCoupon = ShopMaterialSourceRelationship::findOne(['id' => $coupon['coupon_share_id']]);
                        if (!empty($shareCoupon) && $shareCoupon['material_id'] > 0 && $shareCoupon['material_type'] == ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON) {
                            $materialCoupon = ShopMaterialCoupon::findOne(['id' => $shareCoupon['material_id']]);
                            if ((!empty($materialCoupon) && $materialCoupon['coupon_id'] == $coupon['coupon_id'])) {
                                $couponShareId = $coupon['coupon_share_id'];
                            }
                        }
                    }
                    $coupon['coupon_share_id'] = $couponShareId;
                    ShopThirdOrderCoupon::addThirdOrderCoupon($couponWhere, $coupon);
                }
            }

        }
        return $thirdOrderId;
    }

    //添加订单数据
    public static function addThirdOrder($where, $data)
    {
        $orderModel    = ShopThirdOrder::find()->where($where)->one();
        $oldAttributes = !empty($orderModel) ? clone $orderModel : null;
        $orderModel    = !empty($oldAttributes) ? $oldAttributes : new ShopThirdOrder();
        foreach ($data as $k => $v) {
            if (empty($v)) {
                unset($data[$k]);
            }
        }
        $orderModel->setAttributes($data);
        if (!$orderModel->validate()) {
            return false;
        }
        !empty($oldAttributes) ? $orderModel->update() : $orderModel->save();
        return $orderModel->id;
    }

    //获取单条订单信息
    public static function getOrderDetail($orderNo)
    {
        $field = ['id', 'order_no', 'pay_time', 'status', 'payment_amount', 'payment_method', 'scrm_store_id', 'buy_name',
            'buy_phone', 'receiver_name', 'receiver_phone', 'receiver_state', 'receiver_city', 'receiver_district',
            'receiver_town', 'receiver_address', 'order_share_id', 'guide_id'];
        $info  = self::find()->select($field)->where(['order_no' => $orderNo])->asArray()->with(['product' => function ($query) {
            return $query->select('third_order_id,name,product_number,price');
        }])->with(['coupon' => function ($query) {
            return $query->select('third_order_id,coupon_share_id,coupon_id,coupon_name,coupon_desc')->with(['share' => function ($queryOne) {
                return $queryOne->select('id,user_id')->with(['user' => function ($queryTwo) {
                    $queryTwo->select('id,name');
                }]);
            }]);
        }])->with(['user' => function ($query) {
            return $query->select('id,name');
        }])->one();
        if (empty($info)) {
            throw new InvalidDataException('该订单不存在！');
        }
        //优惠券
        if (isset($info['coupon']) && !empty($info['coupon'])) {
            foreach ($info['coupon'] as &$cv) {
                if (isset($cv['share']['user']['id']) && !empty($cv['share']['user']['id'])) {
                    $cv['guide_id']   = $cv['share']['user']['id'];
                    $cv['guide_name'] = $cv['share']['user']['name'];
                }
                unset($cv['share']);
            }
        }
        $payTypeList = ShopCustomerOrder::getPayType(1);
        //商品以及分享人
        $info['status']           = $info['status'] == 2 ? '退款' : '正常';
        $info['payment_method']   = $info['payment_method'] > 0 ? $payTypeList[$info['payment_method']] : '其他';
        $info['receiver_address'] = $info['receiver_state'] . $info['receiver_city'] . $info['receiver_district'] . $info['receiver_town'] . $info['receiver_address'];
        $info['scrm_store_name']  = ShopCustomerOrder::getStoreName($info['scrm_store_id']);
        $info['product']          = $info['product'] ?: [];
        $info['coupon']           = $info['coupon'] ?: [];
        $info['share_user_name']  = $info['user'] ? $info['user']['name'] : '';
        $info['share_user_id']    = $info['user'] ? $info['user']['id'] : '';
        return $info;
    }

    //生成签名
    public static function sign($params, $app_secret)
    {
        ksort($params);
        $signStrASCII = '';
        foreach ($params as $key => $val) {
            $signStrASCII .= $key . $val;
        }
        $signMD5Str = $app_secret . $signStrASCII . $app_secret;
        return md5($signMD5Str);
    }

    //验证签名
    public static function checkSign($params, $app_secret)
    {
        $sign = $params['sign'];
        unset($params['sign']);
        if ($sign === self::sign($params, $app_secret)) {
            return true;
        } else {
            return false;
        }
    }
}
