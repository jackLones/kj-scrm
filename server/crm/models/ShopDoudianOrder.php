<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_doudian_order}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $shop_id 店铺名称
 * @property string $order_id 订单号
 * @property int $order_status 订单状态
 * @property string $post_receiver 收件人姓名
 * @property string $post_tel 收件人电话
 * @property string $province 省
 * @property string $city 市
 * @property string $town 区
 * @property string $detail 详细地址
 * @property string $post_addr 收件人地址
 * @property string $order_total_amount 订单实付金额（不包含运费）
 * @property string $refund_amount 订单退款金额
 * @property int $b_type 订单APP渠道，0:站外 1:火山 2:抖音 3:头条 4:西瓜 5:微信 6:闪购 7:头条lite版本 8:懂车帝 9:皮皮虾 11:抖音极速版 12:TikTok
 * @property int $c_biz 订单业务类型，1:鲁班广告 2:联盟 4:商城 8:自主经营 10:线索通支付表单 12:抖音门店 14:抖+ 15:穿山甲
 * @property int $pay_type 支付类型，0：货到付款，1：微信，2：支付宝,·4：银行卡,5：余额, 8：Dou分期, 9：新卡支付
 * @property int $sub_b_type 订单来源类型 0:未知 1:app 2:小程序 3:h5
 * @property int $order_type 订单类型，0实物，2普通虚拟，4poi核销，5三方核销，6服务市场
 * @property string $product_info 订单商品详情
 * @property string $coupon_info 优惠券详情
 * @property string $pay_time 支付时间 (pay_type为0货到付款时, 此字段为空)，例如"2018-06-01 12:00:00"
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopDoudianOrder extends \yii\db\ActiveRecord
{
    /**
     * @val  1|备货中
     */
    const STATUS_READY = 1;
    /**
     * @val  2|已发货
     */
    const STATUS_SEND = 2;
    /**
     * @val  3|已取消
     */
    const STATUS_CANCEL = 3;
    /**
     * @val  4|已完成
     */
    const STATUS_FINISH = 4;
    /**
     * @val  5|退款中
     */
    const STATUS_REFUND_ING = 5;
    /**
     * @val  6|退款成功
     */
    const STATUS_REFUND_Y = 6;

    /**
     * @val  1|正在等待拉取订单
     */
    const PULL_ORDER_WAIT = 1;

    /**
     * @val  2|开始拉取订单
     */
    const PULL_ORDER_START = 2;

    /**
     * @val  3|订单拉去已完成
     */
    const PULL_ORDER_FINISH = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_doudian_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'shop_id', 'order_status', 'b_type', 'c_biz', 'pay_type', 'sub_b_type', 'order_type'], 'integer'],
            [['post_addr', 'product_info', 'coupon_info'], 'string'],
            [['order_total_amount', 'refund_amount'], 'number'],
            [['add_time', 'update_time'], 'safe'],
            [['order_id', 'post_receiver', 'post_tel'], 'string', 'max' => 225],
            [['province', 'city', 'town'], 'string', 'max' => 50],
            [['detail', 'pay_time'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => Yii::t('app', 'ID'),
            'corp_id'            => Yii::t('app', '授权的企业ID'),
            'shop_id'            => Yii::t('app', '店铺id'),
            'order_id'           => Yii::t('app', '订单号'),
            'order_status'       => Yii::t('app', '订单状态'),
            'post_receiver'      => Yii::t('app', '收件人姓名'),
            'post_tel'           => Yii::t('app', '收件人电话'),
            'province'           => Yii::t('app', '省'),
            'city'               => Yii::t('app', '市'),
            'town'               => Yii::t('app', '区'),
            'detail'             => Yii::t('app', '详细地址'),
            'post_addr'          => Yii::t('app', '收件人地址'),
            'order_total_amount' => Yii::t('app', '订单实付金额（不包含运费）'),
            'refund_amount'      => Yii::t('app', '订单退款金额'),
            'b_type'             => Yii::t('app', '订单APP渠道，0:站外 1:火山 2:抖音 3:头条 4:西瓜 5:微信 6:闪购 7:头条lite版本 8:懂车帝 9:皮皮虾 11:抖音极速版 12:TikTok'),
            'c_biz'              => Yii::t('app', '订单业务类型，1:鲁班广告 2:联盟 4:商城 8:自主经营 10:线索通支付表单 12:抖音门店 14:抖+ 15:穿山甲'),
            'pay_type'           => Yii::t('app', '支付类型，0：货到付款，1：微信，2：支付宝,·4：银行卡,5：余额, 8：Dou分期, 9：新卡支付'),
            'sub_b_type'         => Yii::t('app', '订单来源类型 0:未知 1:app 2:小程序 3:h5'),
            'order_type'         => Yii::t('app', '订单类型，0实物，2普通虚拟，4poi核销，5三方核销，6服务市场'),
            'product_info'       => Yii::t('app', '订单商品详情'),
            'coupon_info'        => Yii::t('app', '优惠券详情'),
            'pay_time'           => Yii::t('app', '支付时间 (pay_type为0货到付款时, 此字段为空)，例如\"2018-06-01 12:00:00\"'),
            'add_time'           => Yii::t('app', '添加时间'),
            'update_time'        => Yii::t('app', '更新时间'),
        ];
    }

    public function getShop()
    {
        return $this->hasOne(ShopDoudian::className(), ['id' => 'shop_id'])->select('id,shop_name');
    }

    //获取字段别名
    public static function getFieldsAliasName($field, $type = 0)
    {

        $bTypeList   = [
            '1'  => '站外',
            '2'  => '火山',
            '3'  => '抖音',
            '4'  => '头条',
            '5'  => '西瓜',
            '6'  => '微信',
            '7'  => '闪购',
            '8'  => '头条lite版本',
            '9'  => '懂车帝',
            '11' => '皮皮虾',
            '12' => '抖音极速版',
        ];
        $statusList  = [
            '1' => '备货中',
            '2' => '已发货',
            '3' => '已取消',
            '4' => '已完成',
            '5' => '退款中',
            '6' => '退款成功',
        ];
        $cBizList    = [
            '1'  => '鲁班广告',
            '2'  => '联盟',
            '4'  => '商城',
            '8'  => '自主经营',
            '10' => '线索通支付表单',
            '12' => '抖音门店',
            '14' => '抖+',
            '15' => '穿山甲'
        ];
        $payTypeList = [
            '0' => '货到付款', '1' => '微信', '2' => '支付宝', '4' => '银行卡', '5' => '余额', '8' => 'Dou分期', '9' => '新卡支付'
        ];

        $filesList['order_status'] = $statusList;
        $filesList['b_type']       = $bTypeList;
        $filesList['c_biz']        = $cBizList;
        $filesList['pay_type']     = $payTypeList;
        if ($type != 0) {
            return $filesList[$field][$type];
        }
        $obj = [];
        foreach ($filesList[$field] as $ok => $ov) {
            $obj[] = ['id' => $ok, 'name' => $ov];
        }
        return $obj;
    }


    //获取订单状态
    public static function changeOrderStatus($status = 0)
    {
        $statusList = [
            '1'  => '在线支付订单待支付；货到付款订单待确认',
            '2'  => '备货中（只有此状态下，才可发货）',
            '3'  => '已发货',
            '4'  => '已取消：1.用户未支付时取消订单；2.用户超时未支付，系统自动取消订单；3.货到付款订单，用户拒收',
            '5'  => '已完成：1.在线支付订单，商家发货后，用户收货、拒收或者15天无物流更新；2.货到付款订单，用户确认收货',
            '6'  => '退货中-用户申请',
            '7'  => '退货中-商家同意退货',
            '8'  => '退货中-客服仲裁',
            '9'  => '已关闭-退货失败',
            '10' => '退货中-客服同意',
            '11' => '退货中-用户已填物流',
            '12' => '退货成功-商户同意',
            '13' => '退货中-再次客服仲裁',
            '14' => '退货中-客服同意退款',
            '15' => '退货-用户取消',
            '16' => '退款中-用户申请(备货中)',
            '17' => '退款中-商家同意(备货中)',
            '21' => '退款成功-订单退款（备货中，用户申请退款，最终退款成功）',
            '22' => '退款成功-订单退款 (已发货时，用户申请退货，最终退货退款成功)',
            '24' => '退货成功-商户已退款 (特指货到付款订单，已发货时，用户申请退货，最终退货退款成功)',
            '25' => '退款失败-用户取消(备货中)',
            '26' => '退款失败-商家拒绝（备货中）',
            '27' => '退货中-等待买家处理（已发货，商家拒绝用户退货申请）',
            '28' => '退货失败（已发货，商家拒绝用户退货申请，客服仲裁支持商家）',
            '29' => '退货中-等待买家处理（已发货，用户填写退货物流，商家拒绝）',
            '30' => '退款中-退款申请（已发货，用户申请仅退款）',
            '31' => '退款申请取消（已发货，用户申请仅退款时，取消申请）',
            '32' => '退款中-商家同意（已发货，用户申请仅退款，商家同意申请）',
            '33' => '退款中-商家拒绝（已发货，用户申请仅退款，商家拒绝申请）',
            '34' => '退款中-客服仲裁（已发货，用户申请仅退款，商家拒绝申请，买家申请客服仲裁）',
            '35' => '退款中-客服同意（已发货，用户申请仅退款，商家拒绝申请，客服仲裁支持买家）',
            '36' => '退款中-支持商家（已发货，用户申请仅退款，商家拒绝申请，客服仲裁支持商家）',
            '37' => '已关闭-退款失败（已发货，用户申请仅退款时，退款关闭）',
            '38' => '退款成功-售后退款（特指货到付款订单，已发货，用户申请仅退款时，最终退款成功）',
            '39' => '退款成功-订单退款（已发货，用户申请仅退款时，最终退款成功）'
        ];
        if ($status != 0) {
            return $statusList[$status];
        }
        return $statusList;
    }

    //获取任务状态缓存值
    public static function getCacheStatus($corpId, $shopId)
    {
        $cacheKey = 'shop_doudian_pull_order_' . $corpId . '_' . $shopId;
        $status   = \Yii::$app->cache->get($cacheKey);
        if (empty($status)) {
            return ['task_code' => 0, 'task_msg' => '暂无拉取任务'];
        } else {
            $msg = [
                self::PULL_ORDER_WAIT   => '订单拉取任务正排队中！请等待',
                self::PULL_ORDER_START  => '订单拉取任务正在执行中！请等待',
                self::PULL_ORDER_FINISH => '订单拉取成功！',
            ];
            return ['task_code' => $status, 'task_msg' => $msg[$status]];
        }
    }

    //设置任务状态缓存值
    public static function setCacheStatus($corpId, $shopId, $status)
    {
        $cacheKey = 'shop_doudian_pull_order_' . $corpId . '_' . $shopId;
        \Yii::$app->cache->set($cacheKey, $status, 7200);
    }

    //添加订单 数据
    public static function addOrder($where, $data)
    {
        $orderModel    = self::find()->where($where)->one();
        $oldAttributes = !empty($orderModel) ? clone $orderModel : null;
        $orderModel    = !empty($oldAttributes) ? $oldAttributes : new ShopDoudianOrder();
        $orderModel->setAttributes($data);
        if (!$orderModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($orderModel));
        }
        !empty($oldAttributes) ? $orderModel->update() : $orderModel->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_order_' . $where['corp_id']);
        return $orderModel->id;
    }

    //订单详情
    public static function getOrderDetail($shopId, $orderNo)
    {
        $field = ['order_id', 'pay_time', 'order_status', 'order_status', 'post_receiver', 'post_tel', 'post_addr',
            'order_total_amount', 'b_type', 'c_biz', 'pay_type', 'product_info'];
        $info  = self::find()->select($field)->where(['shop_id' => $shopId, 'order_id' => $orderNo])->asArray()->one();
        if (empty($info)) {
            throw new InvalidDataException('该订单不存在！');
        }
        $info['b_type_name']   = ShopDoudianOrder::getFieldsAliasName('b_type', $info['b_type']);
        $info['c_biz_name']    = ShopDoudianOrder::getFieldsAliasName('c_biz', $info['c_biz']);
        $info['pay_type_name'] = ShopDoudianOrder::getFieldsAliasName('pay_type', $info['pay_type']);
        $info['product_info']  = json_decode($info['product_info'], true);
        $info['status_name']   = ShopDoudianOrder::getFieldsAliasName('order_status', $info['order_status']);
        return $info;

    }
}
