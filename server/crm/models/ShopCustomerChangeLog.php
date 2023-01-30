<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_customer_change_log}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $cus_id 顾客id
 * @property string $table_name 日志表名
 * @property string $log_id 对应日志表记录id,多条逗号隔开
 * @property string $title 变更事件 例:消费/评级/导购
 * @property string $type 事件类型 例:线下/淘宝
 * @property string $description 变更具体内容
 * @property string $add_time 变更时间
 */
class ShopCustomerChangeLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_change_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'cus_id','log_id'], 'integer'],
            [['description'], 'required'],
            [['description'], 'string'],
            [['add_time'], 'safe'],
            [['table_name', 'title', 'type'], 'string', 'max' => 100],
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
            'table_name'  => Yii::t('app', '日志表名'),
            'log_id'      => Yii::t('app', '对应日志表记录id'),
            'title'       => Yii::t('app', '变更事件 例:消费/评级/导购'),
            'type'        => Yii::t('app', '事件类型 例:线下/淘宝'),
            'description' => Yii::t('app', '变更具体内容'),
            'add_time'    => Yii::t('app', '变更时间'),
        ];
    }

    /*
     * 添加日志
     * */
    public static function addLog($data){
        $logModel = new ShopCustomerChangeLog();
        $logModel->setAttributes($data);
        if (!$logModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($logModel));
        }
        return $logModel->save();
    }
}
