<?php


namespace app\models;


use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%shop_customer_rfm_default}}".
 *
 * @property int $id
 * @property int $frequency 频率0低1⾼
 * @property int $recency 近度0低1⾼
 * @property int $monetary 额度0低1⾼
 * @property string $default_name 默认名称
 * @property int $type 消费数据:1开启 0未开启
 * @property string $add_time 入库时间
 */
class ShopCustomerRfmDefault extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_customer_rfm_default}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['frequency', 'recency', 'monetary', 'type'], 'integer'],
            [['default_name'], 'string', 'max' => 100],
            [['add_time'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app', 'ID'),
            'frequency'    => Yii::t('app', '频率0低1⾼'),
            'recency'      => Yii::t('app', '近度0低1⾼'),
            'monetary'     => Yii::t('app', '额度0低1⾼'),
            'default_name' => Yii::t('app', '默认名称'),
            'type'         => Yii::t('app', '消费数据:1开启 0未开启'),
            'add_time'     => Yii::t('app', '入库时间'),
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlias()
    {
        return $this->hasOne(ShopCustomerRfmAlias::className(), ['rfm_id' => 'id']);
    }

}