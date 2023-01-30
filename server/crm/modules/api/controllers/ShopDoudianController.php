<?php


namespace app\modules\api\controllers;


use app\components\InvalidDataException;
use app\models\ShopCustomer;
use app\models\ShopDoudian;
use app\models\ShopDoudianCategory;
use app\models\ShopDoudianConfig;
use app\models\ShopDoudianOrder;
use app\modules\api\components\WorkBaseController;
use app\queue\PullDoudianJob;
use app\queue\PullOrderJob;
use app\queue\ShopPushJob;
use Imactool\Jinritemai\DouDianApp;
use Imactool\Jinritemai\OAuthService;
use yii\caching\TagDependency;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShopDoudianController extends WorkBaseController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'pull-order-list' => ['POST'],
                    'order-list'      => ['POST'],
                    'shop-list'       => ['POST'],
                    'all-shop'        => ['POST']
                ],
            ],
        ]);
    }

    /**
     * 手动拉取订单
     * @url  http://{host_name}/api/shop-doudian/pull-order-list
     *
     * @throws InvalidDataException
     */
    public function actionPullOrderList()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }

        $startTime = \Yii::$app->request->post('start_time', 0);
        $endTime   = \Yii::$app->request->post('end_time', 0);
        $shopId    = \Yii::$app->request->post('shop_id', 0);
        $size      = \Yii::$app->request->post('size', 10);
        if (empty($startTime) || empty($endTime)) {
            throw new InvalidDataException('缺少时间参数！');
        }
        if (empty($shopId)) {
            throw new InvalidDataException('缺少店铺id！');
        }

        //缓存时间间隔-三秒内仅仅能拉取一次
        $cacheKey  = 'shop_doudian_pull_order_limit_' . $this->corp->id . '_' . $shopId;
        $isRequest = \Yii::$app->cache->get($cacheKey);
        if ($isRequest) {
            throw new InvalidDataException('请求频繁！3秒后再试！');
        } else {
            \Yii::$app->cache->set($cacheKey, 1, 3);
        }

        //检查之前拉去的状态
        $task_status = ShopDoudianOrder::getCacheStatus($this->corp->id, $shopId);
        if ($task_status['task_code'] == ShopDoudianOrder::PULL_ORDER_START || $task_status['task_code'] == ShopDoudianOrder::PULL_ORDER_WAIT) {
            //throw new InvalidDataException($task_status['task_msg']);
        }
        //拉取订单参数
        $params = [
            'start_time'   => date('Y/m/d H:i:s', strtotime($startTime)),
            'end_time'     => date('Y/m/d H:i:s', strtotime($endTime)),
            'order_by'     => 'update_time',
            'is_desc'      => 1,
            'size'         => $size,
            'order_status' => ['2', '3', '5', '101'],
            'page'         => 1
        ];

        //查询店铺配置
        $shopDetail = ShopDoudian::findOne(['corp_id' => $this->corp->id, 'id' => $shopId]);
        if (empty($shopDetail)) {
            throw new InvalidDataException('店铺信息错误！');
        }
        ShopDoudianOrder::setCacheStatus($this->corp->id, $shopId, ShopDoudianOrder::PULL_ORDER_WAIT);

        //手动拉取数据
        $auto = \Yii::$app->request->post('auto', 1);
        if ($auto) {
            ShopDoudianOrder::setCacheStatus($this->corp->id, $shopId, ShopDoudianOrder::PULL_ORDER_START);
            $config  = ShopDoudianConfig::getConfig();
            $service = new DouDianApp($config);
            $obj = $service->shopApp($shopDetail['shop_id'], $shopDetail['refresh_token']);
            if (empty(ShopDoudianCategory::findOne(['corp_id' => $this->corp->id, 'shop_id' => $shopId]))) {
                $this->getDouCategory($obj, $shopDetail['id']);
            }
            $this->getDouOrder($obj, $params, $shopId);
            TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_order_' . $this->corp->id);
            TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_' . $this->corp->id);
            ShopDoudianOrder::setCacheStatus($this->corp->id, $shopId, ShopDoudianOrder::PULL_ORDER_FINISH,600);
            return ShopDoudianOrder::getCacheStatus($this->corp->id, $shopId);
        } else {
            //队列拉取数据
            \Yii::$app->sq->push(new PullOrderJob([
                'method'  => 'dou_dian_order',
                'corp_id' => $this->corp->id,
                'shop_id' => $shopId,
                'params'  => $params
            ]));
            return ShopDoudianOrder::getCacheStatus($this->corp->id, $shopId);
        }


    }


    private function getDouCategory($obj, $selfShopId, $cid = 0)
    {
        $responseData = $obj->Shop->getShopCategory(['cid' => $cid]);
        if (!empty($responseData['data'])) {
            foreach ($responseData['data'] as &$v) {
                $where    = ['corp_id' => $this->corp->id,
                             'shop_id' => $selfShopId,
                             'cid'     => $v['id']
                ];
                $category = [
                    'corp_id'   => $this->corp->id,
                    'shop_id'   => $selfShopId,
                    'cid'       => $v['id'],
                    'name'      => $v['name'],
                    'level'     => $v['level'],
                    'parent_id' => $v['parent_id'],
                    'is_leaf'   => $v['is_leaf'] == 'true' ? 1 : 0,
                    'enable'    => $v['parent_id'] == 'true' ? 1 : 0
                ];
                ShopDoudianCategory::addCategory($where, $category);
                $this->getDouCategory($obj, $selfShopId, $v['id']);
            }
        }
        return $responseData;
    }

    private function getDouOrder($obj, $params, $shopId)
    {
        $responseData = $obj->Order->getOrderList($params);
        $result       = $responseData['data']['list'];
        if (empty($result)) {
            return [];
        }
        foreach ($result as $v) {
            if (empty($v['pay_time'])) continue;
            //自订单商品信息
            $product      = [];
            $orderStatus  = $this->getOrderStatus($v['order_status']);
            $refundAmount = 0;
            if (!empty($v['child'])) {
                $finalStatus = [];
                foreach ($v['child'] as $pv) {
                    $fStatus = $this->getOrderStatus($pv['final_status']);
                    if ($fStatus == ShopDoudianOrder::STATUS_REFUND_Y) {
                        $refundAmount += $pv['total_amount'];
                    }
                    $finalStatus[] = $fStatus;
                    $product[]     = [
                        'product_name'      => $pv['product_name'],
                        'shipped_num'       => $pv['shipped_num'],//已发货的商品数量
                        'total_amount'      => $pv['total_amount'],//实付金额
                        'order_status'      => $pv['order_status'],
                        'order_status_name' => ShopDoudianOrder::changeOrderStatus($pv['order_status']),
                        'final_status'      => $pv['final_status'],
                        'final_status_name' => ShopDoudianOrder::changeOrderStatus($pv['final_status']),
                        'status_name'       => ShopDoudianOrder::getFieldsAliasName('order_status', $fStatus)
                    ];
                    unset($pv);
                }
                if (in_array(ShopDoudianOrder::STATUS_REFUND_Y, $finalStatus)) {
                    $orderStatus = ShopDoudianOrder::STATUS_REFUND_Y;
                } else if (in_array(ShopDoudianOrder::STATUS_REFUND_ING, $finalStatus)) {
                    $orderStatus = ShopDoudianOrder::STATUS_REFUND_ING;
                }
            }

            $postAddr = [];
            if (isset($v['post_addr']['province']['name']) && !empty($v['post_addr']['province']['name'])) {
                $postAddr['province'] = $v['post_addr']['province']['name'];
            }
            if (isset($v['post_addr']['city']['name']) && !empty($v['post_addr']['city']['name'])) {
                $postAddr['city'] = $v['post_addr']['city']['name'];
            }
            if (isset($v['post_addr']['town']['name']) && !empty($v['post_addr']['town']['name'])) {
                $postAddr['town'] = $v['post_addr']['town']['name'];
            }
            if (isset($v['post_addr']['detail']) && !empty($v['post_addr']['detail'])) {
                $postAddr['detail'] = $v['post_addr']['detail'];
            }
            $item  = [
                'corp_id'            => $this->corp->id,
                'shop_id'            => $shopId,
                'order_id'           => $v['order_id'],
                'order_status'       => $orderStatus,
                'post_receiver'      => $v['post_receiver'],
                'post_tel'           => $v['post_tel'],
                'post_addr'          => isset($postAddr['province']) && !empty($postAddr['province']) ? implode('', $postAddr) : '',
                'province'           => isset($postAddr['province']) ? $postAddr['province'] : '',
                'city'               => isset($postAddr['city']) ? $postAddr['city'] : '',
                'town'               => isset($postAddr['town']) ? $postAddr['town'] : '',
                'detail'             => isset($postAddr['detail']) ? $postAddr['detail'] : '',
                'order_total_amount' => $v['order_total_amount'],
                'refund_amount'      => $refundAmount,
                'b_type'             => $v['b_type'],
                'c_biz'              => $v['c_biz'],
                'pay_type'           => $v['pay_type'],
                'sub_b_type'         => $v['sub_b_type'],
                'order_type'         => $v['order_type'],
                'product_info'       => json_encode($product),
                'coupon_info'        => json_encode($v['coupon_info']),
                'pay_time'           => $v['pay_time']
            ];
            $where = [
                'corp_id'  => $this->corp->id,
                'shop_id'  => $shopId,
                'order_id' => $v['order_id']
            ];
            ShopDoudianOrder::addOrder($where, $item);
        }
        //递归分页请求
        if ($responseData['data']['count'] > 0 && $params['page'] < 3) {
            $params['page'] = $params['page'] + 1;
            $this->getDouOrder($obj, $params, $shopId);
        }
    }

    private function getOrderStatus($status)
    {
        $orderStatus = 0;
        switch ($status) {
            case 1:
                $orderStatus = 0;
                break;
            case 2:
                $orderStatus = 1;
                break;
            case 3:
                $orderStatus = 2;
                break;
            case 4:
                $orderStatus = 3;
                break;
            case 5:
                $orderStatus = 4;
                break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 25:
            case 26:
            case 27:
            case 28:
            case 29:
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 35:
            case 36:
            case 37:
                $orderStatus = 5;
                break;
            case 12:
            case 21:
            case 22:
            case 24:
            case 38:
            case 39:
                $orderStatus = 6;
                break;
            default:
                $orderStatus = $status;
                break;
        }
        return $orderStatus;
    }

    /**
     * 抖店-店铺列表
     * @url  http://{host_name}/api/shop-doudian/all-shop
     * @throws InvalidDataException
     */
    public function actionAllShop()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $tags     = 'shop_doudian_' . $this->corp->id;
        $cacheKey = $tags . '_' . 'all_shop';
        $return   = \Yii::$app->cache->get($cacheKey);
        if (empty($return)) {
            $return = ShopDoudian::find()
                ->select('shop_name as storeName,id as key')
                ->where(['corp_id' => $this->corp->id, 'auth_status' => ShopDoudian::AUTH_Y])
                ->asArray()->all();
            foreach ($return as &$v) {
                $v['key'] = intval($v['key']);
            }
            \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => $tags]));
        }
        return $return;
    }

    /**
     * 抖店-店铺列表
     * @url  http://{host_name}/api/shop-doudian/shop-list
     * @throws InvalidDataException
     */
    public function actionShopList()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $page      = \Yii::$app->request->post('page', 1);
        $pageSize  = \Yii::$app->request->post('page_size', 15);
        $offset    = ($page - 1) * $pageSize;
        $shopModel = ShopDoudian::find()->where(['corp_id' => $this->corp->id]);

        $isChoose = false;
        if ($pageSize > 15) {
            $isChoose = true;
        }
        $cacheKey = "shop_doudian_" . $this->corp->id . '_' . $page;
        //TODO::先不缓存 等待绑定功能之后再缓存
        TagDependency::invalidate(\Yii::$app->cache, "shop_doudian_" . $this->corp->id);
        $return   = \Yii::$app->cache->get($cacheKey);
        if ($isChoose || empty($return) || empty($return['count']) ) {
            $return['count'] = $shopModel->count();
            $result          = $shopModel->limit($pageSize)->offset($offset)->asArray()->with('category')->all();
            foreach ($result as &$cv) {
                unset($cv['shop_id']);
                $cv['key']           = $cv['id'];
                $cv['shop_id']       = $cv['id'];
                $cv['category_list'] = '经营类目：';
                if (!empty($cv['category'])) {
                    $category_list = [];
                    foreach ($cv['category'] as $iv) {
                        $category_list[] = $iv['name'];
                    }
                    $cv['category_list'] .= implode('、', $category_list);
                } else {
                    $cv['category_list'] .= '暂无';

                }
                $cv['auth_status'] = $cv['auth_status'] == 1 ? '已授权' : '失败';
                $cv['task_status'] = ShopDoudianOrder::getCacheStatus($this->corp->id, $cv['id']);
                unset($cv['expires_in']);
                unset($cv['refresh_token']);
                unset($cv['access_token']);
                unset($cv['scope']);
                unset($cv['category']);
                unset($cv['add_time']);
                unset($cv['id']);
            }
            $return['result'] = $result;
            \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => ['shop_doudian_' . $this->corp->id]]));
        }
        return $return;
    }

    /**
     * 抖店-订单列表
     * @url  http://{host_name}/api/shop-doudian/order-list
     * @throws InvalidDataException
     */
    public function actionOrderList()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $page     = \Yii::$app->request->post('page', 1);
        $pageSize = \Yii::$app->request->post('page_size', 15);
        $offset   = ($page - 1) * $pageSize;

        $shopId       = \Yii::$app->request->post('shop_id', '');
        $orderId      = \Yii::$app->request->post('order_id', '');
        $orderStatus  = \Yii::$app->request->post('order_status', '');
        $postReceiver = \Yii::$app->request->post('post_receiver', '');
        $postTel      = \Yii::$app->request->post('post_tel', '');
        $startTime    = \Yii::$app->request->post('start_time', 0);
        $endTime      = \Yii::$app->request->post('end_time', 0);

        $orderModel = ShopDoudianOrder::find()->where(['corp_id' => $this->corp->id]);

        $isChoose = false;
        if ($pageSize > 15) {
            $isChoose = true;
        }
        if (!empty($orderId)) {
            $isChoose   = true;
            $orderModel = $orderModel->andFilterWhere(['like', 'order_id', $orderId]);
        }
        if (!empty($postReceiver)) {
            $isChoose   = true;
            $orderModel = $orderModel->andFilterWhere(['like', 'post_receiver', $postTel]);
        }
        if (!empty($postTel)) {
            $isChoose   = true;
            $orderModel = $orderModel->andFilterWhere(['like', 'post_tel', $postTel]);
        }
        if (!empty($orderStatus)) {
            $isChoose   = true;
            $orderModel = $orderModel->andWhere(['order_status' => $orderStatus]);
        }
        if (!empty($shopId)) {
            $isChoose   = true;
            $orderModel = $orderModel->andWhere(['shop_id' => $shopId]);
        }
        if (!empty($startTime) && !empty($endTime)) {
            $isChoose   = true;
            $orderModel = $orderModel->andFilterWhere(['between', 'pay_time', $startTime, $endTime]);
        }
        $cacheKey = "shop_doudian_order_" . $this->corp->id . '_' . $shopId . '_' . $page;
        $return   = \Yii::$app->cache->get($cacheKey);

        if ($isChoose || empty($return)) {
            $return          = [];
            $return['count'] = $orderModel->count();
            $results         = $orderModel->limit($pageSize)->offset($offset)->asArray()->orderBy(['pay_time' => SORT_DESC])->with('shop')->all();
            $result          = [];
            foreach ($results as $k => $rv) {
                $result[$k]['key']                = $rv['id'];
                $result[$k]['pay_time']           = date('Y年m月d日 H:i:s', strtotime($rv['pay_time']));
                $result[$k]['shop_name']          = $rv['shop']['shop_name'];
                $result[$k]['order_id']           = $rv['order_id'];
                $result[$k]['status_name']        = ShopDoudianOrder::getFieldsAliasName('order_status', $rv['order_status']);
                $result[$k]['post_receiver']      = $rv['post_receiver'];
                $result[$k]['post_tel']           = $rv['post_tel'];
                $result[$k]['province']           = $rv['province'];
                $result[$k]['city']               = $rv['city'];
                $result[$k]['town']               = $rv['town'];
                $result[$k]['detail']             = $rv['detail'];
                $result[$k]['post_addr']          = $rv['post_addr'];
                $result[$k]['order_total_amount'] = $rv['order_total_amount'];
                $result[$k]['b_type_name']        = ShopDoudianOrder::getFieldsAliasName('b_type', $rv['b_type']);
                $result[$k]['c_biz_name']         = ShopDoudianOrder::getFieldsAliasName('c_biz', $rv['c_biz']);
                $result[$k]['pay_type_name']      = ShopDoudianOrder::getFieldsAliasName('pay_type', $rv['pay_type']);
                $result[$k]['product_info']       = json_decode($rv['product_info'], true);
            }
            $return['count']       = $orderModel->count();
            $return['result']      = $result;
            $return['status_list'] = ShopDoudianOrder::getFieldsAliasName('order_status');
            \Yii::$app->cache->set($cacheKey, $return, null, new TagDependency(['tags' => "shop_doudian_order_" . $this->corp->id]));
        }
        return $return;

    }

    /**
     * 抖店-获取店铺授权地址
     * @url  http://{host_name}/api/shop-doudian/get-auth
     * @throws InvalidDataException
     */
    public function actionGetAuth()
    {
        if (empty($this->corp->id)) {
            throw new InvalidDataException('企业id参数错误！');
        }
        $config = ShopDoudianConfig::getConfig();
        if (empty($config)) {
            throw new InvalidDataException('未配置app_key！');
        }
        $service = new DouDianApp($config);
        $authUrl = $service->Auth->generateAuthUrl($this->corp->id);
        return ['auth_url' => $authUrl];
    }

}