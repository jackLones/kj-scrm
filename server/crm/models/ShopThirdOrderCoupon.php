<?php

namespace app\models;

use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_third_order_coupon}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $third_order_id 第三方订单id
 * @property int $coupon_id 第三方券id
 * @property string $coupon_name 券名称
 * @property string $coupon_desc  券的使用描述（例如抵扣13元）
 * @property string $coupon_share_id  电商优惠券分享记录ID
 * @property string $add_time 添加时间
 */
class ShopThirdOrderCoupon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_third_order_coupon}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'third_order_id', 'coupon_id', 'coupon_share_id'], 'integer'],
            [['add_time'], 'safe'],
            [['coupon_name'], 'string', 'max' => 100],
            [['coupon_desc'], 'string', 'max' => 225],
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
            'third_order_id'  => Yii::t('app', '第三方订单id'),
            'coupon_id'       => Yii::t('app', '第三方券id'),
            'coupon_name'     => Yii::t('app', '券名称'),
            'coupon_desc'     => Yii::t('app', ' 券的使用描述（例如抵扣13元）'),
            'coupon_share_id' => Yii::t('app', '电商优惠券分享记录ID'),
            'add_time'        => Yii::t('app', '添加时间'),
        ];
    }
    //关联分享明细
    public function getShare()
    {
        return $this->hasOne(ShopMaterialSourceRelationship::className(), ['id' => 'coupon_share_id']);
    }
    //查询订单优惠券
    public static function getData($where, $field)
    {
        $cacheKey = 'shop_third_order_coupon_' . json_encode($where) . '_' . json_encode($field);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($where, $field) {
            return self::find()->where($where)->select($field)->asArray()->all();
        }, null, new TagDependency(['tags' => 'shop_third_order_coupon']));
    }

    //添加订单优惠券信息
    public static function addThirdOrderCoupon($where, $data)
    {
        $couponModel   = ShopThirdOrderCoupon::find()->where($where)->one();
        $oldAttributes = !empty($couponModel) ? clone $couponModel : null;
        $couponModel   = !empty($oldAttributes) ? $couponModel : new ShopThirdOrderCoupon();
        $couponModel->setAttributes($data);
        if (!$couponModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($couponModel));
        }
        !empty($oldAttributes) ? $couponModel->update() : $couponModel->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_third_order_coupon');
        return $couponModel->id;
    }
}
