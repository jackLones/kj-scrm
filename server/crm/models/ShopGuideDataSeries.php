<?php

namespace app\models;

use Yii;
use yii\caching\TagDependency;
use yii\db\Expression;

/**
 * This is the model class for table "{{%shop_guide_data_series}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $store_id 门店id
 * @property int $guide_id 导购id
 * @property float $monetary 日销售额
 * @property int $add_user_number 日拉新数
 * @property string $add_day 日期天
 * @property string $add_month 日期月
 * @property string $add_year 日期年
 * @property string $add_time 操作时间
 */
class ShopGuideDataSeries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_guide_data_series}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'store_id', 'guide_id', 'add_user_number'], 'integer'],
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
            'id'              => Yii::t('app', 'ID'),
            'corp_id'         => Yii::t('app', '授权的企业ID'),
            'store_id'        => Yii::t('app', '门店id'),
            'guide_id'        => Yii::t('app', '导购id'),
            'monetary'        => Yii::t('app', '日销售额'),
            'add_user_number' => Yii::t('app', '日拉新数'),
            'add_day'         => Yii::t('app', '日期天'),
            'add_month'       => Yii::t('app', '日期月'),
            'add_year'        => Yii::t('app', '日期年'),
            'add_time'        => Yii::t('app', '操作时间')
        ];
    }


    //统计每日数据-导购
    public static function dealData($today = '',$corpId=0)
    {
        $today     = $today ?: date('Y-m-d', strtotime("-1 day"));
        $starData = $today. ' 00:00:00';
        $endData  = $today. ' 23:59:59';

        $saveData = [];
        //导购主表
        $workUserModel =  WorkUser::find()->alias('u')
            ->leftJoin("{{%auth_store_user}} as a", "u.id=a.user_id and a.status=1");
        if(!empty($corpId)){
            $workUserModel = $workUserModel->where(['u.corp_id'=>$corpId]);
        }
        $allStore =$workUserModel
            ->select('u.id,u.corp_id,a.store_id')
            ->asArray()
            ->all();
        //日销售额
        $shopCustomerAmountModel =  ShopCustomerOrder::find()
            ->select('guide_id,store_id,sum(payment_amount) as amount');
        if(!empty($corpId)){
            $shopCustomerAmountModel = $shopCustomerAmountModel->where(['corp_id'=>$corpId]);
        }
        $monetaryResult =$shopCustomerAmountModel
            ->andFilterWhere(['between', 'pay_time', $starData, $endData])
            ->groupBy('guide_id','store_id')
            ->asArray()
            ->all();
        foreach ($monetaryResult as $v) {
            $monetary[$v['store_id']][$v['guide_id']] = $v['amount'];
        }

        //拉新数量
        $shopCustomerNumberModel = ShopCustomerOrder::find()
            ->select('store_id,guide_id,count(distinct cus_id) as number')
            ->where(['>','store_id',0]);
        if(!empty($corpId)){
            $shopCustomerNumberModel = $shopCustomerNumberModel->where(['corp_id'=>$corpId]);
        }
        $addUserList = $shopCustomerNumberModel
            ->andWhere(['first_buy'=>1])
            ->andFilterWhere(['between', 'pay_time', $starData, $endData])
            ->groupBy('store_id,guide_id')
            ->asArray()
            ->all();

        foreach ($addUserList as $v) {
            $addUserNumber[$v['store_id']][$v['guide_id']] = $v['number'];
        }

        //汇合数据
        foreach ($allStore as $v) {
            $re                    = [];
            $re['corp_id']         = $v['corp_id'];
            $re['guide_id']        = $v['id'];
            $re['store_id']        = !empty($v['store_id']) ? $v['store_id'] : 0;
            $re['add_user_number'] = isset($addUserNumber[$v['store_id']][$v['id']]) ? $addUserNumber[$v['store_id']][$v['id']] : 0;//拉新数
            $re['monetary']        = isset($monetary[$v['store_id']][$v['id']]) ? $monetary[$v['store_id']][$v['id']] : 0;//日销售额
            $re['add_year']        = date("Y", (strtotime($today)));
            $re['add_month']       = date("Y-m", (strtotime($today) ));
            $re['add_day']         = date("Y-m-d", (strtotime($today)));
            $saveData[]           = $re;
        }
        TagDependency::invalidate(\Yii::$app->cache, 'shop_guide_data_series');
        return self::addData($saveData);
    }

    //添加订单 数据
    public static function addData($data)
    {
        $model = new ShopGuideDataSeries();
        foreach ($data as $attributes){
            $model->isNewRecord = true;
            $model->setAttributes($attributes);
            $where = [
                'add_day'=>$attributes['add_day'],
                'store_id'=>$attributes['store_id'],
                'guide_id'=>$attributes['guide_id'],
                'corp_id'=>$attributes['corp_id']
            ];
            $res = ShopGuideDataSeries::find()
                ->where($where)
                ->count();
            if( empty($res) ){
                $model->save() && $model->id = 0;
            }
        }
        return true;
    }


}
