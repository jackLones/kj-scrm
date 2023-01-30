<?php


namespace app\modules\api\controllers;


use app\components\InvalidDataException;
use app\models\ShopDoudian;
use app\models\ShopDoudianConfig;
use app\models\ShopMaterialPage;
use app\models\ShopMaterialProduct;
use app\models\ShopMaterialProductGroup;
use app\models\ShopMaterialCoupon;
use app\models\ShopMaterialSourceRelationship;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderSet;
use app\queue\ShopPushJob;
use app\util\SUtils;
use Imactool\Jinritemai\DouDianApp;
use yii\console\Controller;

class ShopCustomerTaskController extends Controller
{
    /**
     * 订单推送接口
     * @url  http://{host_name}/api/shop-customer-task/push-order
     */

    public function actionPushOrder()
    {
        $methodList = [
            'order_list', 'product_list', 'page', 'coupon', 'review_count'
        ];
        $params     = \Yii::$app->request->post();
        if (empty($params['app_key'])) {
            return json_encode(['code' => 10003, 'msg' => '缺少参数app_key']);
        }
        if (empty($params['method'])) {
            return json_encode(['code' => 10004, 'msg' => '缺少参数method']);
        }
        if (!in_array($params['method'], $methodList)) {
            return json_encode(['code' => 10005, 'msg' => 'method参数错误！']);
        }
        $config = ShopThirdOrderSet::find()->where(['shop_api_key' => $params['app_key']])->asArray()->one();
        if (empty($config)) {
            return json_encode(['code' => 10001, 'msg' => '未配置key和密钥!' . $params['app_key']]);
        }

        //验证签名
        $signResult = ShopThirdOrder::checkSign($params, $config['shop_api_secret']);
        if (!$signResult) {
            return json_encode(['code' => 10002, 'msg' => '签名失败']);
        }
        unset($params['app_key']);
        unset($params['sign']);
        if (!isset($params['data']) || empty($params['data'])) {
            return json_encode(['code' => 10006, 'msg' => '缺少推送数据！']);
        }
        $data = json_decode($params['data'], true);
        //检验数据
        $error = $this->checkData($data, $params['method']);
        if (!empty($error)) {
            return json_encode(['code' => 10007, 'msg' => $error]);
        }
        //放入队列	
        \Yii::$app->sq->push(new ShopPushJob([
            'config' => $config,
            'data'   => $data,
            'method' => $params['method'],
        ]));
        return json_encode(['code' => 0, 'msg' => '推送成功！']);
    }

    //验证数据
    private function checkData($data, $method)
    {
        $error = [];
        switch ($method) {
            case 'coupon':
                $error = $this->checkCoupon($data);
                break;
            case 'product_list':
                $error = $this->checkProduct($data);
                break;
            case 'page':
                $error = $this->checkPage($data);
                break;
            case 'order_list':
                $error = $this->checkOrder($data);
                break;
            default:
                break;
        }
        return $error;
    }

    //验证优惠券
    private function checkCoupon($data)
    {
        $checkRe = [];
        foreach ($data as $dv) {
            $checkModel = new ShopMaterialCoupon();
            $checkModel->setAttributes($dv);
            if (!$checkModel->validate()) {
                $checkRe[] = '优惠券id为' . $dv['coupon_id'] . '：' . SUtils::modelError($checkModel);
            }
            unset($dv);
            unset($checkModel);
        }
        return $checkRe;
    }

    //验证商品
    private function checkProduct($data)
    {
        $error = [];
        foreach ($data as $dv) {
            $checkModel = new ShopMaterialProduct();
            $checkModel->setAttributes($dv);
            if (!$checkModel->validate()) {
                $checkRe[] = '商品id为' . $dv['id'] . '：' . SUtils::modelError($checkModel);
            }
            unset($dv);
            unset($checkModel);
        }
        return $error;
    }

    //验证页面
    private function checkPage($data)
    {
        $error = [];
        foreach ($data as $dv) {
            $checkModel = new ShopMaterialPage();
            $checkModel->setAttributes($dv);
            if (!$checkModel->validate()) {
                $checkRe[] = '页面id为' . $dv['page_id'] . '：' . SUtils::modelError($checkModel);
            }
            unset($dv);
            unset($checkModel);
        }
        return $error;
    }

    //验证页面
    private function checkOrder($data)
    {
        $error = [];
        foreach ($data as $dv) {
            $checkModel = new ShopThirdOrder();
            $checkModel->setAttributes($dv);
            if (!$checkModel->validate()) {
                $checkRe[] = '订单号为' . $dv['order_no'] . '：' . SUtils::modelError($checkModel);
            }
            unset($dv);
            unset($checkModel);
        }
        return $error;
    }

    /**
     * 抖店订单
     * @url  http://{host_name}/api/shop-customer-task/doudian-auth
     */
    public function actionDoudianAuth()
    {
        $code       = \Yii::$app->request->post('code');
        $corp_id    = \Yii::$app->request->post('state');
        $config     = ShopDoudianConfig::getConfig();
        $service    = new DouDianApp($config);
        $accessInfo = $service->Auth->requestAccessToken($code);
        if (!empty($accessInfo['err_no'])) {
            return json_encode($accessInfo);
        }
        $shopId = ShopDoudian::updateData($corp_id, $accessInfo['data']);
        //刷新access_token 更新缓存
        $service->shopApp($shopId,$accessInfo['data']['refresh_token']);
        return json_encode(['code' => 0, 'msg' => '绑定成功！']);
    }

}