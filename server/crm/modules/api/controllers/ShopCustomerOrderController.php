<?php

namespace app\modules\api\controllers;

use app\components\InvalidDataException;
use app\models\ShopCustomer;
use app\models\ShopCustomerGuideRelation;
use app\models\ShopCustomerOrder;
use app\models\ShopCustomerRfmSetting;
use app\models\ShopCustomerStoreConfig;
use app\models\ShopDataSeries;
use app\models\ShopDoudian;
use app\models\ShopDoudianOrder;
use app\models\ShopGuideDataSeries;
use app\models\ShopMaterialSourceRelationship;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderCoupon;
use app\models\ShopThirdOrderProduct;
use app\models\ShopThirdOrderSet;
use app\modules\api\components\WorkBaseController;
use app\queue\LogJob;
use app\queue\UpdateHistoryDataJob;
use app\util\ShopCustomUtil;
use Codeception\Command\Console;
use dovechen\yii2\weWork\components\HttpUtils;
use yii\caching\TagDependency;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\queue\PullOrderJob;
use Imactool\Jinritemai\DouDianApp;

class ShopCustomerOrderController extends WorkBaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'list'                => ['POST'],
                    'third-order-list'    => ['POST'],
                    'third-order-product' => ['POST'],
                    'get-order-set'       => ['POST'],
                    'save-order-set'      => ['POST'],
                    'pull-order'          => ['POST'],
                    'other-Order-detail'  => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * 顾客订单列表接口
     * @url  http://{host_name}/api/shop-customer-order/list
     */
    public function actionList()
    {
        $post     = \Yii::$app->request->post();
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;

        $orderNo  = \Yii::$app->request->post('order_no', 0);
        $buyPhone = \Yii::$app->request->post('buy_phone', 0);
        $guideId  = \Yii::$app->request->post('guide_id', '');
        $storeId  = \Yii::$app->request->post('store_id', '');
        $cusId    = \Yii::$app->request->post('cus_id', '');

        $nickname      = \Yii::$app->request->post('nickname', '');
        $source        = \Yii::$app->request->post('source', '');
        $paymentMethod = \Yii::$app->request->post('payment_method', '');
        $addTimeStart  = \Yii::$app->request->post('add_time_start', ''); //开始时间
        $addTimeEnd    = \Yii::$app->request->post('add_time_end', ''); //结束时间
        $orderList     = ShopCustomerOrder::find()->where(['corp_id' => $this->corp->id]);
        //分享素材
        $shareId      = \Yii::$app->request->post('share_id', '');
        $materialType = \Yii::$app->request->post('material_type', '');
        if (!empty($shareId)) {
            //优惠券分享
            if ($materialType == ShopMaterialSourceRelationship::MATERIAL_TYPE_COUPON) {
                $orderDetail = ShopThirdOrderCoupon::find()->alias('s')
                    ->select('o.order_no')
                    ->leftJoin('{{%shop_third_order}} o', 'o.id = s.third_order_id')
                    ->where(['s.coupon_share_id' => $shareId])->asArray()->all();
                if (!empty($orderDetail)) {
                    $orderNoList = array_column($orderDetail, 'order_no');
                    $orderList   = $orderList->andWhere(['order_no' => $orderNoList]);
                }
            } else {//商品或者页面分享
                $orderList = $orderList->andWhere(['order_share_id' => $shareId]);
            }
        }

        if (!empty($orderNo)) {
            $orderList = $orderList->andWhere(['order_no' => $orderNo]);
        }
        if (!empty($buyPhone)) {
            $orderList = $orderList->andWhere(['like', 'buy_phone', $buyPhone]);
        }
        if (!empty($guideId)) {
            $orderList = $orderList->andWhere(['guide_id' => $guideId]);
        }

        if (!empty($storeId)) {
            $storeIdArr = strstr($storeId, ',') ? explode(',', $storeId) : [$storeId];
            $storeIds   = [];
            foreach ($storeIdArr as $v) {
                if (strstr($v, '-') !== false) {
                    $tmpId = explode('-', $v);
                    if ($tmpId[1] == 's') {
                        $storeIds[] = $tmpId[0];
                    }
                } else {
                    $storeIds[] = $v;
                }
            }
            $orderList = $orderList->andWhere(['in', 'store_id', $storeIds]);
        }

        if (!empty($source) || $source === 0) {
            $orderList = $orderList->andWhere(['source' => $source]);
        }
        if (!empty($paymentMethod) || $paymentMethod === 0) {
            $orderList = $orderList->andWhere(['payment_method' => $paymentMethod]);
        }
        if (!empty($nickname)) {
            $orderList = $orderList->andWhere(['or', ['like', 'name', $nickname], ['like', 'buy_name', $nickname]]);
        }
        if (!empty($addTimeStart) && !empty($addTimeEnd)) {
            $orderList = $orderList->andFilterWhere(['between', 'pay_time', $addTimeStart, $addTimeEnd]);
        }
        //顾客订单
        if (isset($cusId) && !empty($cusId)) {
            $orderList = $orderList->andWhere(['cus_id' => $cusId]);
        }
        $source      = ShopCustomerOrder::getSource();
        $payType     = ShopCustomerOrder::getPayType();
        $sourceList  = ShopCustomerOrder::getSource(1);
        $payTypeList = ShopCustomerOrder::getPayType(1);
        $count       = $orderList->count();
        $info        = $orderList->limit($pageSize)->offset($offset)->asArray()->orderBy(['pay_time' => SORT_DESC])->all();
        $result      = [];

        foreach ($info as $v) {
            $from = '暂无';
            if (!empty($v['order_share_id'])) {
                $shareDetail = ShopMaterialSourceRelationship::findOne(['id' => $v['order_share_id']]);
                $fromName    = ShopMaterialSourceRelationship::getChatName($this->corp->id, $shareDetail['channel'], $shareDetail['chat_id']);
                $from        = $shareDetail['channel'] == 1 ? '好友会话' : '群聊';
                if (!empty($fromName)) {
                    $from .= '(' . $fromName . ')';
                }
            }
            if ($v['source'] == ShopCustomerOrder::SOURCE_PIG) {
                $shop_name = ShopThirdOrderSet::getData($this->corp->id, 'shop_name');
            } else if ($v['source'] == ShopCustomerOrder::SOURCE_DOU) {
                $shop_name = $v['other_store_name'];//抖店记录的就是第三方店铺名称
            }
            $result[] = [
                'key'               => $v['id'],
                'customer'          => $v['name'] . (!empty($v['buy_phone']) ? ("(" . $v['buy_phone'] . ')') : ''),
                'source'            => $sourceList[$v['source']] . '(' . $shop_name . ')',//TODO::当系统类型增加时店铺名称待修改
                'order_no'          => $v['order_no'],
                'status'            => $v['status'] == ShopCustomerOrder::STATUS_REAL ? '正常' : ($v['status'] == ShopCustomerOrder::STATUS_REFUND ? '退款' : '未知'),
                'payment_amount'    => $v['payment_amount'],
                'from'              => $from,
                'payment_method'    => $v['payment_method'] > 0 ? $payTypeList[$v['payment_method']] : '其他',
                'guide_name'        => $v['guide_name'],
                'payment_method_id' => $v['payment_method'],
                'store_name'        => $v['store_name'],
                'pay_time'          => $v['pay_time'],
                'other_store_id'    => $v['other_store_id'],
                'source_value'      => $v['source'],
            ];
        }
        return [
            'source'   => $source,
            'pay_type' => $payType,
            'where'    => $post,
            'count'    => $count,
            'result'   => $result,
        ];
    }

    /**
     * 第三方订单列表接口
     * @url  http://{host_name}/api/shop-customer-order/third-order-list
     */
    public function actionThirdOrderList()
    {
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;

        $orderNo       = \Yii::$app->request->post('order_no', '');//订单号
        $buyPhone      = \Yii::$app->request->post('buy_phone', '');
        $receiverName  = \Yii::$app->request->post('receiver_name', '');
        $orderStatus   = \Yii::$app->request->post('order_status', '');
        $paymentMethod = \Yii::$app->request->post('payment_method', '');
        $addTimeStart  = \Yii::$app->request->post('add_time_start', ''); //开始时间
        $addTimeEnd    = \Yii::$app->request->post('add_time_end', ''); //结束时间

        $orderList = ShopThirdOrder::find()->alias('o')->where(['corp_id' => $this->corp->id]);

        if (!empty($orderNo)) {
            $orderList = $orderList->andWhere(['like', 'o.order_no', $orderNo]);
        }
        if (!empty($buyPhone)) {
            $orderList = $orderList->andWhere(['like', 'o.buy_phone', $buyPhone]);
        }
        if (!empty($receiverName)) {
            $orderList = $orderList->andWhere(['like', 'o.receiver_name', $receiverName]);
        }
        if ($orderStatus > 0) {
            $orderList = $orderList->andWhere(['o.status' => $orderStatus]);
        }
        if (!empty($paymentMethod)) {
            $orderList = $orderList->andWhere(['o.payment_method' => $paymentMethod]);
        }
        if (!empty($addTimeStart) && !empty($addTimeEnd)) {
            $orderList = $orderList->andFilterWhere(['between', 'o.pay_time', $addTimeStart, $addTimeEnd]);
        }

        $count       = $orderList->count();
        $orderList   = $orderList->with(['product' => function ($query) {
            return $query->select('third_order_id,name,product_number,price');
        }])->with(['coupon' => function ($query) {
            return $query->select('third_order_id,coupon_share_id,coupon_id,coupon_name,coupon_desc')
                ->with(['share' => function ($queryOne) {
                    return $queryOne->select('id,user_id')->with(['user' => function ($queryTwo) {
                        $queryTwo->select('id,name');
                    }]);
                }]);
        }])->with(['user' => function ($query) {
            return $query->select('id,name');
        }]);
        $info        = $orderList->limit($pageSize)->offset($offset)->asArray()->orderBy(['o.pay_time' => SORT_DESC])->all();
        $result      = [];
        $payTypeList = ShopCustomerOrder::getPayType(1);
        foreach ($info as $v) {

            if (isset($v['coupon']) && !empty($v['coupon'])) {
                foreach ($v['coupon'] as &$cv) {
                    if (isset($cv['share']['user']['id']) && !empty($cv['share']['user']['id'])) {
                        $cv['guide_id']   = $cv['share']['user']['id'];
                        $cv['guide_name'] = $cv['share']['user']['name'];
                    }
                    unset($cv['share']);
                }
            }


            $result[] = [
                'key'               => $v['id'],
                'id'                => $v['id'],
                'order_no'          => $v['order_no'],
                'status'            => $v['status'] == ShopThirdOrder::STATUS_REAL ? '正常' : ($v['status'] == ShopThirdOrder::STATUS_REFUND ? '退款' : '未知'),
                'payment_amount'    => $v['payment_amount'],
                'payment_method'    => $v['payment_method'] > 0 ? $payTypeList[$v['payment_method']] : '其他',
                'buy_name'          => $v['buy_name'],
                'buy_phone'         => $v['buy_phone'],
                'pay_time'          => $v['pay_time'],
                'receiver_name'     => $v['receiver_name'],
                'receiver_phone'    => $v['receiver_phone'],
                'receiver_state'    => $v['receiver_state'],
                'receiver_city'     => $v['receiver_city'],
                'receiver_district' => $v['receiver_district'],
                'receiver_town'     => $v['receiver_town'],
                'receiver_address'  => $v['receiver_state'] . $v['receiver_city'] . $v['receiver_district'] . $v['receiver_town'] . $v['receiver_address'],
                'scrm_store_id'     => $v['scrm_store_id'],
                'scrm_store_name'   => ShopCustomerOrder::getStoreName($v['scrm_store_id']),
                'product'           => $v['product'] ?: [],
                'coupon'            => $v['coupon'] ?: [],
                'share_user_name'   => $v['user'] ? $v['user']['name'] : '',
                'share_user_id'     => $v['user'] ? $v['user']['id'] : ''
            ];
        }
        //支付方式
        $payType = ShopCustomerOrder::getPayType();
        //拉取订单的任务状态
        $cusStatus = \Yii::$app->cache->get(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $this->corp->id);
        if (isset($cusStatus) && ($cusStatus > 0 || $cusStatus === -1)) {
            $task = ['task_status' => $cusStatus, 'task_msg' => \Yii::$app->cache->get(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $this->corp->id)];
        } else {
            $task = ['task_status' => 0, 'task_msg' => '未执行拉取任务'];
        }
        return [
            'pay_type' => $payType,
            'count'    => $count,
            'result'   => $result,
            'task'     => $task
        ];

    }


    /**
     * 获取订单商品信息
     * @url  http://{host_name}/api/shop-customer-order/third-order-product
     */
    public function actionThirdOrderProduct()
    {
        $orderId = \Yii::$app->request->post('order_id', 0);//订单号
        if (empty($orderId)) {
            throw new InvalidDataException('缺少参数订单id！');
        }
        $where['third_order_id'] = $orderId;
        $fields                  = ['name', 'product_number', 'price'];
        return ShopThirdOrderProduct::getData($where, $fields);
    }


    /**
     * 获取店铺配置信息
     * @url  http://{host_name}/api/shop-customer-order/get-order-set
     */
    public function actionGetOrderSet()
    {
        $config = ShopThirdOrderSet::getData($this->corp->id);
        if (empty($config)) {
            $corp    = json_encode(['corp_id' => $this->corp->id, 'time' => date('Y-m-d H:i:s', time())]);
            $randStr = str_shuffle('abcdefghijklmnopqistuvwxyz01234567890');
            $rand    = substr($randStr, 0, 6);
            $config  = [
                'id'               => 0,
                'corp_id'          => $this->corp->id,
                'shop_name'        => '',
                'shop_api_key'     => $this->corp->corpid . '_' . $rand,
                'shop_api_secret'  => ShopThirdOrderSet::encryptDecrypt($this->corp->corpid . '_' . $rand, $corp, 0),
                'order_pull_url'   => '',
                'third_api_key'    => '',
                'third_api_secret' => ''
            ];
        }
        $config['push_url'] = \Yii::$app->params['site_url'] . '/api/shop-customer-task/push-order';
        return $config;
    }

    /**
     * 保存订阅地址等信息 创建店铺接口
     * @url  http://{host_name}/api/shop-customer-order/save-order-set
     */
    public function actionSaveOrderSet()
    {
        $post            = \Yii::$app->request->post();
        $data            = [];
        $data['corp_id'] = $this->corp->id;
        if (isset($post['shop_name'])) {
            $data['shop_name'] = $post['shop_name'];
        }
        if (isset($post['shop_api_key'])) {
            $data['shop_api_key'] = $post['shop_api_key'];
        }
        if (isset($post['shop_api_secret'])) {
            $data['shop_api_secret'] = $post['shop_api_secret'];
        }
        if (isset($post['order_pull_url'])) {
            $data['order_pull_url'] = $post['order_pull_url'];
        }
        if (isset($post['third_api_key'])) {
            $data['third_api_key'] = $post['third_api_key'];
        }
        if (isset($post['third_api_secret'])) {
            $data['third_api_secret'] = $post['third_api_secret'];
        }
        if (isset($post['status'])) {
            $data['status'] = $post['status'];
        }
        if (isset($post['id'])) {
            $data['id'] = $post['id'];
        }
        if (empty($data)) {
            throw new InvalidDataException('参数为空!');
        }
        return ShopThirdOrderSet::updateConfig($this->corp->id, $this->user->uid, $data);
    }

    /**
     * 拉取第三方订单
     * @url  http://{host_name}/api/shop-customer-order/pull-order
     */
    public function actionPullOrder()
    {
        //缓存时间间隔-三秒内仅仅能拉取一次
        $cacheKey  = 'shop_customer_order_pull_order_' . $this->corp->id;
        $isRequest = \Yii::$app->cache->get($cacheKey);
        if ($isRequest) {
            throw new InvalidDataException('请求频繁！3秒后再试！');
        } else {
            \Yii::$app->cache->set($cacheKey, 1, 3);
        }


        //获取数据类型
        $method = \Yii::$app->request->post('method');
        if (empty($method)) {
            throw new InvalidDataException('缺少获取数据类型参数！');
        }
        if (empty($this->corp->id)) {
            throw new InvalidDataException('缺少企业id！');
        }

        //若任务正在执行或者等待中则直接返回结果
        $cusStatus = \Yii::$app->cache->get(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $this->corp->id);
        if (isset($cusStatus) && in_array($cusStatus, [ShopThirdOrder::PUSH_ORDER_STATUS_WAITE, ShopThirdOrder::PUSH_ORDER_STATUS_START])) {
            $msg = ($cusStatus == ShopThirdOrder::PUSH_ORDER_STATUS_WAITE) ? '拉取订单任务等待执行' : '拉取订单任务正在执行';
            //throw new InvalidDataException($msg);
        }

        //获取配置
        $config = ShopThirdOrderSet::getData($this->corp->id);
        if (empty($config)) {
            throw new InvalidDataException('未填写拉取配置!');
        } else if (empty($config['order_pull_url'])) {
            throw new InvalidDataException('未填写拉取地址!');
        } else {
            //未验证则进行验证
            $response = ShopThirdOrder::getThirdOrder($config, 1, 1, 60);
            if ($response['code'] == 0) {
                if ($response['total_num'] == 0) {
                    throw new InvalidDataException('近60天暂无订单数据!');
                }
            } else {
                throw new InvalidDataException($response['message']);
            }
        }

        //业务参数
        $pageSize = \Yii::$app->request->post('page_size', 100);
        $day      = \Yii::$app->request->post('day', 60);

        //执行队列
        $return           = [];
        $jobId            = \Yii::$app->sq->push(new PullOrderJob([
            'page_size' => $pageSize,
            'method'    => $method,
            'day'       => $day,
            'corp_id'   => $this->corp->id,
        ]));
        $return['job_id'] = $jobId;
        $return['status'] = ShopThirdOrder::PUSH_ORDER_STATUS_WAITE;
        $return['msg']    = '拉取订单任务已经加入队列正等待执行';
        //缓存当前执行状态与提示信息
        \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $this->corp->id, $return['status']);
        \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $this->corp->id, $return['msg']);
        return $return;
    }


    /**
     * 获取订单第三方详情
     * @url  http://{host_name}/api/shop-customer-order/other-order-detail
     */
    public function actionOtherOrderDetail()
    {
        $orderNo = \Yii::$app->request->post('order_no');
        $source  = \Yii::$app->request->post('source_value');
        $shop_id = \Yii::$app->request->post('other_store_id');

        if (empty($orderNo) || empty($source) || empty($shop_id)) {
            throw new InvalidDataException('缺少必传参数!');
        }
        $result = [];
        switch ($source){
            case ShopCustomerOrder::SOURCE_PIG:
                $result = ShopThirdOrder::getOrderDetail($orderNo);
                break;
            case ShopCustomerOrder::SOURCE_DOU:
                $result = ShopDoudianOrder::getOrderDetail($shop_id,$orderNo);
                break;
            default:
                throw new InvalidDataException('暂无类型订单!');
                break;
        }
        return $result;
    }

    //导入数据初始化所有数据
    public function actionTest()
    {

        $re = ShopCustomerOrder::clearDouOrder(32);
        return [$re];

        $config = [
            'app_key'    => '6910368270752499215',
            'app_secret' => 'ce3a8c5d-95f7-49d9-b9a5-a5a335ca7efa',
            'service_id' => '10394'
        ];

        $app = new DouDianApp($config);
//
        $params = [
            'start_time' => '2020-01-01 00:00:00',
            'end_time'   => '2022-01-01 00:00:00',
            'order_by'   => 'update_time',
        ];
        $result = $app->Order->getOrderList($params);
        return [$result];

        $authUrl = $app->Auth->generateAuthUrl('state');

        $code       = '772effc9-99b0-4563-80a9-067d33a7005e';
        $accessInfo = $app->Auth->requestAccessToken($code);
        $params     = [
            'start_time' => '2020-01-01 00:00:00',
            'end_time'   => '2022-01-01 00:00:00',
            'order_by'   => 'update_time',
        ];
        $result1    = $app->Order->getOrderList($params);
        $result2    = $app->Shop->getShopCategory(['cid' => 0]);
        $result3    = $app->Shop->getShopCategory(['cid' => 20006]);

        return [$result1, $result2, $result3];

        $re = ShopCustomerOrder::clearOrder(1);
        return [$re];

        return ShopCustomer::clearWorkUser(0, 37883);

        $corp_id = 32;
        $jobId   = \Yii::$app->queue->push(new UpdateHistoryDataJob([
            'corp_id' => $corp_id
        ]));
        echo $jobId;
        die();
    }


}
