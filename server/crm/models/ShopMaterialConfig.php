<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\Cache;
use yii\caching\TagDependency;
use yii\db\StaleObjectException;
use function foo\func;

/**
 * This is the model class for table "{{%shop_material_config}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $product 商品标签，0关闭，1开启
 * @property int $page 页面标签，0关闭，1开启
 * @property int $coupon 优惠券标签，0关闭，1开启
 * @property string $weapp_appid 小程序APPID
 * @property string $weapp_name 小程序名称
 * @property string $coupon_image 小程序分享优惠券默认图片
 * @property string $page_image 小程序分享页面默认图片
 * @property int $web_open H5商城是否开启0关闭，1开启
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopMaterialConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_config}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'product', 'page', 'coupon', 'web_open'], 'integer'],
            [['add_time', 'update_time'], 'safe'],
            [['weapp_appid'], 'string', 'max' => 50],
            [['page_image', 'coupon_image','weapp_name'], 'string', 'max' => 225]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app', 'ID'),
            'corp_id'      => Yii::t('app', '授权的企业ID'),
            'product'      => Yii::t('app', '商品标签，0关闭，1开启'),
            'page'         => Yii::t('app', '页面标签，0关闭，1开启'),
            'coupon'       => Yii::t('app', '优惠券标签，0关闭，1开启'),
            'weapp_appid'  => Yii::t('app', '小程序APPID'),
            'weapp_name'   => Yii::t('app', '小程序名称'),
            'coupon_image' => Yii::t('app', '小程序分享优惠券默认图片'),
            'page_image'   => Yii::t('app', '小程序分享页面默认图片'),
            'web_open'     => Yii::t('app', 'H5商城是否开启'),
            'add_time'     => Yii::t('app', '添加时间'),
            'update_time'  => Yii::t('app', '更新时间'),
        ];
    }

    //保存配置
    public static function saveConfig($corpId, $data)
    {

        $config = ShopMaterialConfig::findOne(['corp_id' => $corpId]);
        $new    = 0;
        if (empty($config)) {
            $config = new ShopMaterialConfig();
            $new    = 1;
        }

        //验证
        $config->setAttributes($data);
        if (!$config->validate()) {
            throw new InvalidDataException(SUtils::modelError($config));
        }

        //更新或者保存
        if($new == 1){
            try {
                $config->insert();
            } catch (\Throwable $e) {
                throw new InvalidDataException($e->getMessage());
            }
        }else{
            try {
                $config->update();
            } catch (StaleObjectException $e) {
                throw new InvalidDataException($e->getMessage());
            } catch (\Throwable $e) {
                throw new InvalidDataException($e->getMessage());
            }
        }
        $cacheKey = 'shop_material_config_' . $corpId;
        TagDependency::invalidate(\Yii::$app->cache, $cacheKey);
        return ['result' => 1];
    }

    //获取配置
    public static function getConfig($corpId)
    {
        $cacheKey = 'shop_material_config_' . $corpId;
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corpId) {
            return self::findOne(['corp_id' => $corpId]);
        }, null, new TagDependency(['tags' => $cacheKey]));
    }

}
