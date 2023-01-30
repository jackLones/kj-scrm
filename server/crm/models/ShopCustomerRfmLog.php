<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_customer_rfm_log}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $cus_id 顾客id
 * @property int $rfm_id 等级ID
 * @property string $rfm_name 等级名称
 * @property int $before_rfm_id 之前的等级
 * @property string $before_rfm_name 之前等级名称
 * @property string $add_time 入库时间
 */
class ShopCustomerRfmLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_rfm_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'cus_id', 'rfm_id', 'before_rfm_id'], 'integer'],
            [['add_time'], 'safe'],
            [['rfm_name', 'before_rfm_name'], 'string', 'max' => 100],
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
            'cus_id'          => Yii::t('app', '顾客id'),
            'rfm_id'          => Yii::t('app', '等级ID'),
            'rfm_name'        => Yii::t('app', '等级名称'),
            'before_rfm_id'   => Yii::t('app', '之前的等级'),
            'before_rfm_name' => Yii::t('app', '之前等级名称'),
            'add_time'        => Yii::t('app', '入库时间'),
        ];
    }

    /*
    * 添加日志
    * */
    public static function addLog($data){
        $logModel = new ShopCustomerRfmLog();
        $logModel->setAttributes($data);
        if (!$logModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($logModel));
        }
        $logModel->save();
        return $logModel->id;
    }

}
