<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "{{%shop_doudian}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $shop_id 店铺名称
 * @property string $shop_name 店铺名称
 * @property string $access_token 用于调用API的access_token
 * @property int $expires_in access_token接口调用凭证超时时间，单位（秒），默认有效期：7天
 * @property string $scope 授权作用域，使用逗号,分隔。预留字段
 * @property string $refresh_token 用于刷新access_token的刷新令牌（有效期：14 天）
 * @property int $auth_status 授权状态 0 未授权 1授权 
 * @property string $auth_time 授权时间
 * @property string $update_time 更新时间
 */
class ShopDoudian extends \yii\db\ActiveRecord
{
    //授权状态 授权
    const AUTH_Y = 1 ;
    //授权状态 未授权
    const AUTH_N = 0 ;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_doudian}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'shop_id', 'expires_in', 'auth_status'], 'integer'],
            [['auth_time', 'update_time'], 'safe'],
            [['shop_name', 'access_token', 'scope', 'refresh_token'], 'string', 'max' => 225],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'shop_id' => Yii::t('app', '店铺名称'),
            'shop_name' => Yii::t('app', '店铺名称'),
            'access_token' => Yii::t('app', '用于调用API的access_token'),
            'expires_in' => Yii::t('app', 'access_token接口调用凭证超时时间，单位（秒），默认有效期：7天'),
            'scope' => Yii::t('app', '授权作用域，使用逗号,分隔。预留字段'),
            'refresh_token' => Yii::t('app', '用于刷新access_token的刷新令牌（有效期：14 天）'),
            'auth_status' => Yii::t('app', '授权状态 0 未授权 1授权 '),
            'auth_time' => Yii::t('app', '授权时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }

    public function getCategory(){
        return $this->hasMany(ShopDoudianCategory::className(),['shop_id'=>'id'])
            ->select('shop_id,name')->where(['parent_id'=>0,'enable'=>1]);
    }

    public static function getData($corp_id, $shopId ,$field = '')
    {

        $cacheKey = 'shop_doudian_' . $corp_id . '_' .$shopId. '_' . json_encode($field);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id,$shopId,$field) {
            $setting = self::find()->where(['corp_id' => $corp_id,'id'=>$shopId])->asArray()->one();
            if (!empty($field) && !empty($setting)) {
                return $setting[$field];
            }
            return $setting;
        }, null, new TagDependency(['tags' => 'shop_doudian_' . $corp_id.'_'.$shopId]));


    }

    public static function updateData($corpId,$data){
            $shopId = $data['shop_id'];
            $config = ShopDoudian::findOne(['corp_id' => $corpId,'shop_id'=>$shopId]);
            $new    = 0;
            if (empty($config)) {
                $config = new ShopDoudian();
                $new    = 1;
            }

            //验证
            $data['corp_id'] = $corpId;
            $config->setAttributes($data);
            if (!$config->validate()) {
                return ['message'=>SUtils::modelError($data)];
            }
            //更新或者保存
            if($new == 1){
                $config->insert();
            }else{
                $config->update();
            }
            $cacheKey = 'shop_doudian_' . $corpId.'_'.$config->id;
            TagDependency::invalidate(\Yii::$app->cache, $cacheKey);
            return $config->id;
    }
}
