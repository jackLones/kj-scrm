<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_customer_guide_change_log}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $cus_id 顾客id
 * @property int $guide_id 导购id
 * @property int $store_id 门店id
 * @property int $operator_id 操作员工id
 * @property int $type 操作类型：0解绑  1绑定
 * @property string $add_time 入库时间
 */
class ShopCustomerGuideChangeLog extends \yii\db\ActiveRecord
{
    const ADD_TYPE    = 1;//绑定导购
    const DELETE_TYPE = 0;//解绑导购
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_guide_change_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'cus_id', 'guide_id', 'operator_id', 'type', 'store_id'], 'integer'],
            [['add_time'], 'safe'],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'corp_id'     => Yii::t('app', '授权的企业ID'),
            'cus_id'      => Yii::t('app', '顾客id'),
            'guide_id'    => Yii::t('app', '导购id'),
            'store_id'    => Yii::t('app', '门店id'),
            'operator_id' => Yii::t('app', '操作员工id'),
            'type'        => Yii::t('app', '操作类型：0解绑  1绑定'),
            'add_time'    => Yii::t('app', '入库时间'),
        ];
    }

    public static function addLog($data){
        $logModel = new ShopCustomerGuideChangeLog();
        $logModel->setAttributes($data);
        if ( !$logModel->validate() ) {
            throw new InvalidDataException(SUtils::modelError($logModel));
        }
        $logModel->save();
        return $logModel->id;
    }
}
