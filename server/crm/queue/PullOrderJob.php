<?php

namespace app\queue;

use app\models\ShopDoudian;
use app\models\ShopDoudianCategory;
use app\models\ShopDoudianConfig;
use app\models\ShopDoudianOrder;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderSet;
use Imactool\Jinritemai\DouDianApp;
use Imactool\Jinritemai\OAuthService;
use yii\base\BaseObject;
use yii\caching\TagDependency;
use yii\queue\JobInterface;

class  PullOrderJob extends BaseObject implements JobInterface
{
    public $corp_id;
    public $method;

    public $page_size;
    public $day;

    public $shop_id;
    public $params;


    public function execute($queue)
    {
        //根据拉取数据类型 分发处理
        switch ($this->method) {
            case 'pig_order'://拉取小猪电商订单数据
                $where  = [
                    'corp_id' => $this->corp_id
                ];
                $config = ShopThirdOrderSet::find()->where($where)->asArray()->one();
                //修改任务状态为：开始执行
                \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_KEY . "_" . $this->corp_id, ShopThirdOrder::PUSH_ORDER_STATUS_START);
                \Yii::$app->cache->set(ShopThirdOrder::PUSH_ORDER_STATUS_MSG . "_" . $this->corp_id, '拉取订单任务正在执行！');
                ShopThirdOrder::addOrderArray($this->corp_id, $config, 1, $this->page_size, $this->day);
                break;
            case 'dou_dian_order':
                ShopDoudianOrder::setCacheStatus($this->corp_id, $this->shop_id, ShopDoudianOrder::PULL_ORDER_START);
                //获取店铺配置
                $shopDetail = ShopDoudian::findOne(['corp_id' => $this->corp_id, 'id' => $this->shop_id]);
                //获取总配置
                $config  = ShopDoudianConfig::getConfig();
                $service = new DouDianApp($config);
                /*$code = 'f28def2c-fbd3-4b72-8cd0-dc382a5c2da4';
                $accessInfo = $service->Auth->requestAccessToken($code);*/
                $obj = $service->shopApp($shopDetail['shop_id'], $shopDetail['refresh_token']);
                if (empty(ShopDoudianCategory::findOne(['corp_id' => $this->corp_id, 'shop_id' => $this->shop_id]))) {
                    $this->getDouCategory($obj);
                }
                $this->getDouOrder($obj, $this->params);
                ShopDoudianOrder::setCacheStatus($this->corp_id, $this->shop_id, ShopDoudianOrder::PULL_ORDER_FINISH,600);
                //清除订单以及分类缓存
                TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_order_' . $this->corp_id);
                TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_' . $this->corp_id);
                break;
            default:
                \Yii::error('获取数据类型不存在!', 'PullOrderJob');
                break;
        }
    }

    private function getDouCategory($obj, $cid = 0)
    {
        $responseData = $obj->Shop->getShopCategory(['cid' => $cid]);
        if (!empty($responseData['data'])) {
            foreach ($responseData['data'] as &$v) {
                $where    = ['corp_id' => $this->corp_id,
                             'shop_id' => $this->shop_id,
                             'cid'     => $v['id']
                ];
                $category = [
                    'corp_id'   => $this->corp_id,
                    'shop_id'   => $this->shop_id,
                    'cid'       => $v['id'],
                    'name'      => $v['name'],
                    'level'     => $v['level'],
                    'parent_id' => $v['parent_id'],
                    'is_leaf'   => $v['is_leaf'] == 'true' ? 1 : 0,
                    'enable'    => $v['parent_id'] == 'true' ? 1 : 0
                ];
                ShopDoudianCategory::addCategory($where, $category);
                //循环添加子分类
                //$this->getDouCategory($obj, $v['id']);
            }
        }
        return $responseData;
    }

    private function getDouOrder($obj, $params)
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
                'corp_id'            => $this->corp_id,
                'shop_id'            => $this->shop_id,
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
                'corp_id'  => $this->corp_id,
                'shop_id'  => $this->shop_id,
                'order_id' => $v['order_id']
            ];
            ShopDoudianOrder::addOrder($where, $item);
        }
        //递归分页请求
        if ($responseData['data']['count'] > 0 && $params['page'] < 100) {
            $params['page'] = $params['page'] + 1;
            $this->getDouOrder($obj, $params);
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
                $orderStatus = ShopDoudianOrder::STATUS_READY;
                break;
            case 3:
                $orderStatus = ShopDoudianOrder::STATUS_SEND;
                break;
            case 4:
                $orderStatus = ShopDoudianOrder::STATUS_CANCEL;
                break;
            case 5:
                $orderStatus = ShopDoudianOrder::STATUS_FINISH;
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
                $orderStatus = ShopDoudianOrder::STATUS_REFUND_ING;
                break;
            case 12:
            case 21:
            case 22:
            case 24:
            case 38:
            case 39:
                $orderStatus = ShopDoudianOrder::STATUS_REFUND_Y;
                break;
            default:
                $orderStatus = $status;
                break;
        }
        return $orderStatus;
    }
}