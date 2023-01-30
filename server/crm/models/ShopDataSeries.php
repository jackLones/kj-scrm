<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;
use yii\db\Expression;

/**
 * This is the model class for table "{{%shop_data_series}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $store_id 门店id
 * @property float $monetary 日销售额
 * @property int $add_user_number 日拉新数-首次消费
 * @property int $interaction_number 互动顾客数
 * @property int $consumption_number 消费顾客数
 * @property string $add_day 日期天
 * @property string $add_month 日期月
 * @property string $add_year 日期年
 * @property string $add_time 操作时间
 */
class ShopDataSeries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_data_series}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'store_id', 'add_user_number', 'interaction_number', 'consumption_number'], 'integer'],
            [['monetary'], 'number'],
            [['add_time'], 'safe'],
            [['add_day', 'add_month', 'add_year'], 'string', 'max' => 10],
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
            'store_id'           => Yii::t('app', '门店id'),
            'monetary'           => Yii::t('app', '日销售额'),
            'add_user_number'    => Yii::t('app', '日拉新数'),
            'interaction_number' => Yii::t('app', '互动顾客数'),
            'consumption_number' => Yii::t('app', '消费顾客数'),
            'add_day'            => Yii::t('app', '日期天'),
            'add_month'          => Yii::t('app', '日期月'),
            'add_year'           => Yii::t('app', '日期年'),
            'add_time'           => Yii::t('app', '操作时间'),
        ];
    }

    //根据时间计算总销售额度
    public static function getMonetary($corpId)
    {
        $yesterday = date("Y-m-d", strtotime('-1 day'));
        $agoDay    = date("Y-m-d", strtotime('-2 day'));
        $cacheKey  = 'shop_data_series_' . $corpId . '_' . $yesterday;
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId, $yesterday, $agoDay) {
            //总销售额 昨日销售额 前天销售额 昨天新增顾客数
            $monetary = self::find()
                ->select(new Expression('sum(monetary) as monetary,min(add_day) as min_day,
                    sum(case when add_day ="' . $agoDay . '" then monetary else 0 end) as ago_monetary,
                    sum(case when add_day ="' . $yesterday . '" then add_user_number else 0 end) as yesterday_add_user_number,
                    sum(case when add_day ="' . $yesterday . '" then monetary else 0 end) as yesterday_monetary'
                ))
                ->where(['corp_id' => $corpId])
                ->groupBy('corp_id')
                ->asArray()
                ->one();
            //总顾客数
            $allCustomerNumber             = ShopCustomer::find()->where(['corp_id' => $corpId])->count();
            $result['all_customer_number'] = $allCustomerNumber;
            //近十五天 销售额增量 顾客增量
            $oldDataList = self::find()
                ->select('add_day,
                    sum(monetary) as day_monetary,
                    sum(add_user_number) as day_add_user_number,
                    sum(consumption_number) as day_consumption_number,
                    sum(interaction_number) as day_interaction_number')
                ->where(['corp_id' => $corpId])
                ->andWhere(['>', 'add_day', date("Y-m-d", strtotime('-15 day'))])
                ->groupBy('add_day')
                ->asArray()
                ->all();
            //消费顾客数
            $buyCustomerNumber = ShopCustomer::find()->where(['corp_id' => $corpId])->andWhere(['>', 'consumption_count', 0])->count();
            //总的互动顾客数
            $interactionNumber            = ShopCustomer::find()->where(['corp_id' => $corpId])->andWhere(['>', 'interactive_count', 0])->count();
            $result['min_day']            = !empty($monetary['min_day']) ? $monetary['min_day'] : date("Y-m-d");
            $result['monetary']           = !empty($monetary['monetary']) ? $monetary['monetary'] : 0;
            $result['yesterday_monetary'] = !empty($monetary['yesterday_monetary']) ? $monetary['yesterday_monetary'] : 0;
            if ($monetary['ago_monetary'] == 0) {
                $result['day_monetary_rate']  = 0;
                $result['monetary_rate_type'] = 0;
            } else {
                $dayMonetaryRate              = isset($monetary['ago_monetary']) && $monetary['ago_monetary'] > 0 ? ceil($monetary['yesterday_monetary'] * 100 / $monetary['ago_monetary']) : 0;
                $result['day_monetary_rate']  = $dayMonetaryRate > 100 ? $dayMonetaryRate - 100 : (100 - $dayMonetaryRate);
                $result['monetary_rate_type'] = $dayMonetaryRate > 100 ? 1 : 0;
            }
            $result['yesterday_add_user_number'] = !empty($monetary['yesterday_add_user_number']) ? $monetary['yesterday_add_user_number'] : 0;

            $result['buy_customer_number'] = $buyCustomerNumber ?: 0;
            $result['monetary_rate']       = $allCustomerNumber > 0 ? ceil($buyCustomerNumber * 100 / $allCustomerNumber) : 0;
            $result['interaction_number']  = $interactionNumber ?: 0;
            $result['interaction_rate']    = $allCustomerNumber > 0 ? ceil($interactionNumber * 100 / $allCustomerNumber) : 0;
            $result['old_data_list']       = self::changeDataChart($oldDataList);
            return $result;
        }, null, new TagDependency(['tags' => [$cacheKey, 'shop_data_series']]));
    }

    //根据时间计算销售额 拉新量
    public static function getDataByDate($corpId, $startDate = 0, $endDate = 0)
    {
        $cacheKey = 'shop_data_series_' . $corpId . '_' . $startDate . '_' . $endDate;
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId, $startDate, $endDate) {
            $isMonth = (empty($startDate) || empty($endDate)) ? -1 : ((strtotime($endDate) - strtotime($startDate)) / 3600 / 24 > 31 ? 1 : 0);
            if ($isMonth == 1) {
                $startDate = $isMonth == 1 ? date('Y-m', strtotime($startDate)) : date('Y-m', strtotime('-12 month'));
                $endDate   = $isMonth == 1 ? date('Y-m', strtotime($endDate)) : date('Y-m', time());
                $select    = 'add_month as day,sum(monetary) as monetary,sum(add_user_number) as add_user_number';
                $group     = 'add_month';
            } else {
                $startDate = date('Y-m-d', strtotime($startDate));
                $endDate   = date('Y-m-d', strtotime($endDate));
                $select    = 'add_day as day,sum(monetary) as monetary,sum(add_user_number) as add_user_number';
                $group     = 'add_day';
            }
            $result = self::find()
                ->select(new Expression($select))
                ->where(['corp_id' => $corpId])
                ->andFilterWhere(['between', $group, $startDate, $endDate])
                ->groupBy($group)
                ->asArray()
                ->all();
            //循环
            $resultDay     = [];
            $addTime       = $isMonth == 1 ? '+1 month' : '+1 day';
            $addTimeFormat = $isMonth == 1 ? 'Y-m' : 'Y-m-d';
            foreach ($result as $v) {
                $resultDay[$v['day']] = $v;
            }
            $result = [];
            for ($i = $startDate; $i <= $endDate; $i = date($addTimeFormat, strtotime($addTime, strtotime($i)))) {
                $result[$i]['day']             = $i;
                $result[$i]['monetary']        = isset($resultDay[$i]['monetary']) ? $resultDay[$i]['monetary'] : 0;
                $result[$i]['add_user_number'] = isset($resultDay[$i]['add_user_number']) ? $resultDay[$i]['add_user_number'] : 0;
            }
            return ['result' => self::changeDataChart($result), 'is_month' => $isMonth, 'start_date' => $startDate, 'end_date' => $endDate];
        }, null, new TagDependency(['tags' => ['shop_data_series_' . $corpId, 'shop_data_series']]));
    }

    //根据门店id计算销售额 拉新量
    public static function getDataByStore($corpId, $storeId, $month)
    {
        $cacheKey = 'shop_data_series_' . $corpId . '_' . $month . '_' . json_encode($storeId);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId, $storeId, $month) {
            $afterMonth = date('Y-m', strtotime("-1 month", strtotime($month)));
            $result     = [];
            $allData    = self::find()
                ->select(new Expression('add_month,store_id,sum(monetary) monetary,sum(add_user_number) add_user_number'))
                ->where(['corp_id' => $corpId, 'store_id' => $storeId])
                ->andWhere(['>=', 'add_month', $afterMonth])
                ->groupBy('add_month,store_id')
                ->asArray()
                ->all();
            foreach ($allData as $v) {
                $result[$v['store_id']][$v['add_month']]['monetary']        = $v['monetary'];
                $result[$v['store_id']][$v['add_month']]['add_user_number'] = $v['add_user_number'];
            }
            return $result;
        }, null, new TagDependency(['tags' => ['shop_data_series_' . $corpId, 'shop_data_series']]));
    }

    //根据门店id计算当月每天的数据
    public static function getMonthData($corpId, $storeId, $month)
    {
        $cacheKey = 'shop_data_series_' . $corpId . '_month_' . $month . '_' . json_encode($storeId);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId, $storeId, $month) {
            $result = self::find()
                ->select(new Expression('add_day,sum(monetary) monetary,sum(add_user_number) add_user_number'))
                ->where(['corp_id' => $corpId, 'store_id' => $storeId, 'add_month' => $month])
                ->groupBy('add_day')
                ->asArray()
                ->all();
            $all    = [];
            foreach ($result as $k => &$v) {
                $v['add_day']       = date('m/d', strtotime($v['add_day']));
                $all[$v['add_day']] = $v;
            }
            $day      = $month . '-01';
            $curMonth = $month;
            //到期日期
            $allResult = [];
            while ($curMonth == $month) {
                $keyDay             = date('m/d', strtotime($day));
                $allResult[$keyDay] = isset($all[$keyDay]) ? $all[$keyDay] : ['add_day' => $keyDay, 'monetary' => 0, 'add_user_number' => 0];
                $day                = date('Y-m-d', strtotime('+1 day', strtotime($day)));
                $curMonth           = date('Y-m', strtotime($day));
                if ($day == date('Y-m-d')) {
                    break;
                }
            }
            return self::changeDataChart($allResult);
        }, null, new TagDependency(['tags' => ['shop_data_series_' . $corpId, 'shop_data_series']]));
    }

    //统计每日数据-门店
    public static function dealData($today = '', $corpId = 0)
    {
        if(empty($corpId)){
            return true;
        }
        $today = $today ?: date('Y-m-d', strtotime("-1 day"));
        $starData = $today . ' 00:00:00';
        $endData  = $today . ' 23:59:59';
        $saveData = [];

        $allStore = [];
        //门店主表
        $allAuthStore = AuthStore::find()->select('id')->where(['status' => 1,'corp_id'=>$corpId])->asArray()->all();
        if(!empty($allAuthStore)){
            foreach ($allAuthStore as $sv){
                $allStore[$sv['id']] = $sv;
                unset($sv);
            }
        }
        //日销售额
        $ShopCustomerOrderModel = ShopCustomerOrder::find()
            ->select(new Expression('store_id,sum(payment_amount) as amount,count(distinct cus_id) as consumption_number'))
            ->where(['corp_id' => $corpId]);

        $ShopCustomerOrderModel = $ShopCustomerOrderModel->andFilterWhere(['between', 'pay_time', $starData, $endData]);
        $monetaryResult         = $ShopCustomerOrderModel->groupBy('store_id')->asArray()->all();
        if(!empty($monetaryResult)) {
            foreach ($monetaryResult as $v) {
                $allStore[$v['store_id']]          = ['id' => $v['store_id']];
                $monetary[$v['store_id']]          = $v['amount'];
                $consumptionNumber[$v['store_id']] = $v['consumption_number'];
                unset($v);
            }
        }

        //拉新数量
        $ShopCustomerNumberModel = ShopCustomerOrder::find()
            ->select('store_id,count(distinct cus_id) as number')
            ->where(['corp_id' => $corpId]);
        $ShopCustomerNumberModel = $ShopCustomerNumberModel->andWhere(['first_buy' => 1])
            ->andFilterWhere(['between', 'pay_time', $starData, $endData]);

        $addUserList = $ShopCustomerNumberModel->groupBy('store_id')->asArray()->all();
        if(!empty($addUserList)) {
            foreach ($addUserList as $v) {
                $allStore[$v['store_id']]      = ['id' => $v['store_id']];
                $addUserNumber[$v['store_id']] = $v['number'];
                unset($v);
            }
        }

        //互动顾客数
        $shopCustomerModel = ShopCustomer::find()->alias('c')
            ->leftJoin('{{%shop_customer_guide_relation}} r', 'c.id=r.cus_id')
            ->select('r.store_id,count(distinct c.id) as interaction_number ')
            ->where(['c.corp_id' => $corpId])
            ->andFilterWhere(['between', 'c.last_interactive_time', $starData, $endData]);

        $interactionNumberList = $shopCustomerModel
            ->groupBy('r.store_id')
            ->asArray()
            ->all();
        if(!empty($interactionNumberList)){
            foreach ($interactionNumberList as $v) {
                $allStore[$v['store_id']] = ['id'=>$v['store_id']];
                $interactionNumber[$v['store_id']] = $v['interaction_number'];
            }
        }

        if(!empty($allStore)){
            //汇合数据
            foreach ($allStore as $v) {
                $data                       = [];
                $data['corp_id']            = $corpId;
                $data['store_id']           = $v['id'];
                $data['monetary']           = isset($monetary[$v['id']]) ? $monetary[$v['id']] : 0;//日销售额
                $data['add_user_number']    = isset($addUserNumber[$v['id']]) ? $addUserNumber[$v['id']] : 0;//拉新数
                $data['consumption_number'] = isset($consumptionNumber[$v['id']]) ? $consumptionNumber[$v['id']] : 0;//消费顾客数
                $data['interaction_number'] = isset($interactionNumber[$v['id']]) ? $interactionNumber[$v['id']] : 0;//消费顾客数
                $data['add_day']            = date("Y-m-d", (strtotime($today)));
                $data['add_month']          = date("Y-m", (strtotime($today)));
                $data['add_year']           = date("Y", (strtotime($today)));
                $saveData[]                 = $data;
            }
            TagDependency::invalidate(\Yii::$app->cache, 'shop_data_series');
            self::addData($saveData);
        }
        return true;
    }

    //添加订单 数据
    public static function addData($data)
    {
        $model = new ShopDataSeries();
        foreach ($data as $attributes) {
            $model->isNewRecord = true;
            $model->setAttributes($attributes);
            if (!(ShopDataSeries::find()->where(['add_day' => $attributes['add_day'], 'store_id' => (int)$attributes['store_id'], 'corp_id' => $attributes['corp_id']])->asArray()->one())) {
                $model->save() && $model->id = 0;
            }
        }
        return true;
    }

    //数组转换chart
    public static function changeDataChart($data)
    {
        $result = [];
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                foreach ($v as $k1 => $v1) {
                    ${$k1}[]     = $v1;
                    $result[$k1] = ${$k1};
                }
            }
        }

        return $result;
    }
}
