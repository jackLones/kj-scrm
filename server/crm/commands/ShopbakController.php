<?php


namespace app\commands;

use app\models\ShopCustomer;
use app\models\ShopCustomerGuideRelation;
use app\models\ShopCustomerOrder;
use app\models\ShopDataSeries;
use app\models\ShopGuideDataSeries;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderProduct;
use app\models\ShopThirdOrderSet;
use app\models\UserCorpRelation;
use app\models\WorkExternalContactFollowUser;
use app\queue\UpdateHistoryDataJob;
use dovechen\yii2\weWork\components\HttpUtils;
use yii\console\Controller;
use app\util\ShopCustomUtil;

class ShopbakController extends Controller
{
    /**
     * 清洗scrm订单表 到 顾客订单表以及根据 union_id》发货人号码 》收货人号码 次序 寻找 custtomer表 顾客id 不存在则生成
     */
    public function actionIndex()
    {

        //开启电商零售场景企业数组
        $corp   = UserCorpRelation::find()->where(['uid' => [1,14]])->select('corp_id')->asArray()->all();
        if (empty($corp)) {
            var_dump('暂无企业开启电商零售模块！');
        }
        $corpIdList = array_column($corp, 'corp_id');

        if(empty($corpIdList)){
            var_dump('暂无企业开启电商零售模块！');
            return false;
        }

        //开启循环企业清理数据
        var_dump('清洗数据开始');
        foreach ($corpIdList as $corpId){

            //清理企业微信用户
            ShopCustomer::clearWorkUser($corpId);
            var_dump('企业'.$corpId.':企微用户清理完成');

            //清理非企业微信用户
            ShopCustomer::clearSeaUser($corpId);
            var_dump('企业'.$corpId.':非企微用户清理完成');

            //订单清理脚本
            ShopCustomerOrder::clearOrder($corpId);
            var_dump('订单清理脚本完成');

            //更新用户表rfm信息
            ShopCustomer::taskRfm(['is_del' => 0,'corp_id'=>$corpId]);
            var_dump('更新用户表rfm信息');

            //总的企业数量
            var_dump('循环更新企业'.$corpId.'门店数据');
            $yesterday  = date('Y-m-d', strtotime("-1 day"));
            $starData   = $yesterday . ' 00:00:00';
            $whereStart = ['or', ['>', 'add_time', $starData], ['>', 'update_time', $starData]];
            //昨日该企业第三方订单数据
            $minDay = ShopCustomerOrder::find()
                ->select('min(pay_time) as now')
                ->where(['>', 'pay_time', 0])
                ->andWhere(['corp_id' => $corpId])
                ->andWhere($whereStart)
                ->asArray()
                ->one();
            $startDay = '';
            \Yii::error($minDay,'startShop:'.$corpId);
            if(!empty($minDay) && !empty($minDay['now'])){
                $startDay = date('Y-m-d', strtotime($minDay['now']));
            }
            $startDay = $startDay  ?: $yesterday;
            \Yii::error($startDay,'startShop:one_time'.$corpId);
            $startDay = strtotime($startDay) > strtotime('2000-01-01') ? $startDay : $yesterday;
            \Yii::error($startDay,'startShop:two_time'.$corpId);
            for ($i = $startDay; $i < date('Y-m-d'); $i = date('Y-m-d', strtotime($i) + 3600 * 24)) {
                var_dump($i.'循环更新企业'.$corpId.'门店数据');
                ShopDataSeries::dealData($i, $corpId);
            }
            unset($corpId);
        }

        echo "success!";
        die();
    }


}