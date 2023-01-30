<?php
namespace app\queue;

use app\components\InvalidDataException;
use app\models\ShopCustomer;
use app\models\ShopCustomerOrder;
use app\models\ShopDataSeries;
use app\models\ShopGuideDataSeries;
use app\models\ShopThirdOrder;
use app\models\ShopThirdOrderSet;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class UpdateHistoryDataJob extends BaseObject implements JobInterface
{
    public $corp_id;

    public function execute($queue)
    {
        //订单清理脚本
        ShopCustomerOrder::clearOrder(['corp_id'=>$this->corp_id]);

        //更新用户表rfm信息
        ShopCustomer::taskRfm(['is_del'=>0,'corp_id'=>$this->corp_id]);

        //循环历史数据每日总数据脚本
        $order = ShopCustomerOrder::find()
            ->select('min(pay_time) as pay_time ')
            ->where(['>', 'pay_time', 0])
            ->andWhere(['corp_id'=>$this->corp_id])
            ->asArray()->one();
        for ($i = date('Y-m-d',strtotime($order['pay_time'])); $i < date('Y-m-d'); $i = date('Y-m-d',strtotime($i) + 3600*24) ){
            ShopDataSeries::dealData($i,$this->corp_id);
        }
        echo "success!";
        die();
    }
}