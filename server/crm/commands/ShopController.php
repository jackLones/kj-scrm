<?php


namespace app\commands;

use app\models\ShopCustomer;
use app\models\ShopCustomerGuideRelation;
use app\models\ShopCustomerOrder;
use app\models\ShopDataSeries;
use app\models\ShopDoudian;
use app\models\ShopGuideDataSeries;
use app\models\ShopTaskRecord;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderProduct;
use app\models\ShopThirdOrderSet;
use app\models\WorkExternalContactFollowUser;
use app\queue\UpdateHistoryDataJob;
use dovechen\yii2\weWork\components\HttpUtils;
use yii\console\Controller;
use app\util\ShopCustomUtil;

class ShopController extends Controller
{
    /**
     * 清洗scrm订单表 到 顾客订单表以及根据 union_id》发货人号码 》收货人号码 次序 寻找 custtomer表 顾客id 不存在则生成
     * @param string $corpIdStr
     * @return false
     * @throws \app\components\InvalidDataException
     */
    public function actionIndex($corpIdStr = '')
    {
        //开启电商零售场景企业数组
        if (empty($corpIdStr)) {
            $corpIdList = ShopCustomUtil::getHaShopCustomCropIds();
        } else {//测试传参
            $corpIdList = explode(',', $corpIdStr);
        }

        if (empty($corpIdList)) {
            var_dump('暂无企业开启电商零售模块！');
            return false;
        }
        //开启循环企业清理数据
        var_dump('清洗数据开始');
        foreach ($corpIdList as $corpId) {

            /**
             * 获取第三方商城配置信息
             * 若未配置任何第三方商城则不更新数据
             */
            $thirdConfig = ShopThirdOrderSet::getData($corpId);
            $douConfig   = ShopDoudian::findOne(['corp_id' => $corpId]);

            if(empty($thirdConfig) && empty($douConfig)){
                var_dump('企业' . $corpId . '未配置第三方订单来源暂不需清理！');
                continue;
            }

            //清理企业微信用户
            ShopCustomer::clearWorkUser($corpId);
            var_dump('企业' . $corpId . ':企微用户清理完成');

            //清理非企业微信用户
            ShopCustomer::clearSeaUser($corpId);
            var_dump('企业' . $corpId . ':非企微用户清理完成');

            //订单清理脚本
            if (!empty($thirdConfig)) {
                ShopCustomerOrder::clearOrder($corpId);
                var_dump('订单清理脚本完成');
            }else{
                var_dump('暂无小猪电商!');
            }

            //订单抖店清理脚本
            if (!empty($douConfig)) {
                ShopCustomerOrder::clearDouOrder($corpId);
                var_dump('订单清理脚本完成');
            }else{
                var_dump('暂无抖店!');
            }


            //更新用户表rfm信息
            ShopCustomer::taskRfm(['is_del' => 0, 'corp_id' => $corpId]);
            var_dump('更新用户表rfm信息');

            //总的企业数量
            var_dump('循环更新企业' . $corpId . '门店数据');

            $last_time = ShopTaskRecord::getRecord($corpId, ShopTaskRecord::TYPE_SERIES);
            if (empty($last_time)) {
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $starData  = 0;
            } else {
                $yesterday = date('Y-m-d', strtotime($last_time));
                $starData  = $yesterday . ' 00:00:00';
            }

            //昨日该企业第三方订单数据
            $whereStart = ['or', ['>', 'add_time', $starData], ['>', 'update_time', $starData]];
            $minDay     = ShopCustomerOrder::find()
                ->select('min(pay_time) as now')
                ->where(['>', 'pay_time', 0])
                ->andWhere(['corp_id' => $corpId])
                ->andWhere($whereStart)
                ->asArray()
                ->one();
            $startDay   = '';
            if (!empty($minDay) && !empty($minDay['now'])) {
                $startDay = date('Y-m-d', strtotime($minDay['now']));
            }
            $startDay = $startDay ?: $yesterday;

            $startDay = strtotime($startDay) > strtotime('2000-01-01') ? $startDay : $yesterday;
            for ($i = $startDay; $i < date('Y-m-d'); $i = date('Y-m-d', strtotime($i) + 3600 * 24)) {
                var_dump($i . '循环更新企业' . $corpId . '门店数据');
                ShopDataSeries::dealData($i, $corpId);
            }
            ShopTaskRecord::addRecord($corpId, ShopTaskRecord::TYPE_SERIES);
            unset($corpId);
        }

        echo "success!";
        die();
    }


}