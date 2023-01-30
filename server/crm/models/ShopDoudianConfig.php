<?php

namespace app\models;

use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_doudian_config}}".
 *
 * @property int $id
 * @property string $app_key 证书信息app_key
 * @property string $app_secret 证书信息app_secret
 * @property int $service_id 证书信息service_id
 * @property string $update_time 更新时间
 * @property string $add_time 添加时间
 */
class ShopDoudianConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_doudian_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id'], 'integer'],
            [['update_time', 'add_time'], 'safe'],
            [['app_key', 'app_secret'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'app_key' => Yii::t('app', '证书信息app_key'),
            'app_secret' => Yii::t('app', '证书信息app_secret'),
            'service_id' => Yii::t('app', '证书信息service_id'),
            'update_time' => Yii::t('app', '更新时间'),
            'add_time' => Yii::t('app', '添加时间'),
        ];
    }

    public static function getConfig(){
        $cacheKey = 'shop_doudian_config';
        return \Yii::$app->cache->getOrSet($cacheKey, function (){
           return ShopDoudianConfig::find()->select('app_key,app_secret,service_id')->asArray()->one();
        }, null, new TagDependency(['tags' => 'shop_doudian_config']));

    }
}
