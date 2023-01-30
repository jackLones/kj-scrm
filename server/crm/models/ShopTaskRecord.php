<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_task_record}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $type 任务类型:1 清理企业微信用户 2清理非企业微信用户 3清理订单
 * @property string $last_time 最近更新时间
 * @property string $add_time 发送时间
 */
class ShopTaskRecord extends \yii\db\ActiveRecord
{
    const TYPE_WORK_USER = 1;//清理用户
    const TYPE_SEA_USER  = 2;//清理非企业微信用户
    const TYPE_ORDER     = 3;//清理小猪电商订单
    const TYPE_SERIES    = 4;//门店数据统计
    const TYPE_ORDER_DOU = 5;//清理抖店订单

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_task_record}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'type'], 'integer'],
            [['last_time', 'add_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'        => Yii::t('app', 'ID'),
            'corp_id'   => Yii::t('app', '授权的企业ID'),
            'type'      => Yii::t('app', '任务类型:1 清理企业微信用户 2清理非企业微信用户 3清理订单 '),
            'last_time' => Yii::t('app', '最近更新时间'),
            'add_time'  => Yii::t('app', '发送时间'),
        ];
    }


    //添加记录
    public static function addRecord($corpId, $type)
    {
        $model         = self::find()->where(['corp_id' => $corpId, 'type' => $type])->one();
        $oldAttributes = !empty($model);
        $model         = $oldAttributes ? $model : new ShopTaskRecord();
        $model->setAttributes(['corp_id' => $corpId, 'type' => $type, 'last_time' => date('Y-m-d H:i:s')]);
        if (!$model->validate()) {
            throw new InvalidDataException(SUtils::modelError($model));
        }
        !empty($oldAttributes) ? $model->update() : $model->save();
        return $model->id;
    }

    //获取最近一次记录时间
    public static function getRecord($corpId, $type)
    {
        $data = self::find()->where(['corp_id' => $corpId, 'type' => $type])->asArray()->one();
        return !empty($data) ? $data['last_time'] : 0;
    }

}
