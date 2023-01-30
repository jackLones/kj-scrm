<?php


namespace app\modules\api\controllers;

use app\models\ShopMaterialSourceRelationship;
use app\modules\api\components\BaseController;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class ShopController extends BaseController
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET']
                ],
            ],
        ]);
    }

    /**
     * @url  http://{host_name}/shop/key
     * */
    public function actionIndex()
    {
        $key = \Yii::$app->request->get('key', '');
        //获取原地址
        $share = ShopMaterialSourceRelationship::findOne(['short_flag' => $key]);
        if (empty($share) || empty($share['ext_json'])) {
            $this->redirect(\Yii::$app->params['web_url'] . '/404');
            return true;
        }

        $extJson     = json_decode($share['ext_json'], true);
        $redirectUrl = $extJson['web_url'];
        if (empty($redirectUrl)) {
            $this->redirect(\Yii::$app->params['web_url'] . '/404');
            return true;
        }

        if (strstr($redirectUrl, '?') !== false) {
            $redirectUrl = $redirectUrl . '&scrmsid=' . $share['id'];
        } else {
            $redirectUrl = $redirectUrl . '?scrmsid=' . $share['id'];
        }
        //券则加上券id
        if(isset($extJson['coupon_id']) && !empty($extJson['coupon_id'])){
            $redirectUrl.='&scrmcid='.$extJson['coupon_id'];
        }
        $redirectUrl = rawurlencode($redirectUrl);
        //增加浏览量
        ShopMaterialSourceRelationship::updateAllCounters(['review_count' => 1], ['id' => $share['id']]);

        //组合信息
        $baseUrl = \Yii::$app->params['web_url'];
        $url     = $baseUrl . '/h5/pages/shopMate/transPage?shareid=' . $share['id'] . '&key=' . $key . '&redirect_url=' . $redirectUrl;
        //跳转到H5页面
        $this->redirect($url);
        return true;

    }
}