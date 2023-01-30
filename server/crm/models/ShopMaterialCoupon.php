<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_material_coupon}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $source 来源：1小猪电商 等
 * @property int $shop_api_key 第三方券id
 * @property int $coupon_id 第三方券id
 * @property string $name 券名称
 * @property int $type 券类型 1：优惠券，2：赠送券 3 通用券 4 店铺券 5 兑换券 6代金券
 * @property string $face_money 券面额或者内容
 * @property float $limit_money 使用优惠券的订单金额下限 0：为不限定）
 * @property string|null $desc 使用说明
 * @property int $is_all_product 使用范围 0：全店通用，1：指定商品使用
 * @property int $start_time 生效时间
 * @property int $end_time 过期时间
 * @property string $weapp_url 该优惠券小程序路径
 * @property string $web_url 该优惠券H5的路径
 * @property int $status 是否显示,默认 1，0隐藏，1显示
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopMaterialCoupon extends \yii\db\ActiveRecord
{
    /**
     * @val  1|优惠券
     */
    const TYPE_SAIL = 1;
    /**
     * @val  2|赠送券
     */
    const TYPE_GIFT = 2;
    /**
     * @val  3|通用券
     */
    const TYPE_ALL = 3;
    /**
     * @val  4|店铺券
     */
    const TYPE_STORE = 4;
    /**
     * @val  5|兑换券
     */
    const TYPE_CHANGE = 5;
    /**
     * @val  6|代金券
     */
    const TYPE_MONEY = 6;

    /**
     * @val  0|全店通用
     */
    const IS_PRODUCT_ALL = 0;
    /**
     * @val  1|指定商品使用
     */
    const IS_PRODUCT_SOME = 1;

    /**
     * @val  0|固定区间
     */
    const TIME_TYPE_ZERO = 0;
    /**
     * @val  1|固定时长
     */
    const TIME_TYPE_ONE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_coupon}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'source', 'coupon_id', 'type', 'is_all_product', 'time_type', 'start_time', 'end_time', 'status'], 'integer'],
            [['limit_money'], 'number'],
            [['desc'], 'string'],
            [['add_time', 'update_time'], 'safe'],
            [['name','time_fixed'], 'string', 'max' => 255],
            [['face_money'], 'string', 'max' => 200],
            [['shop_api_key'], 'string', 'max' => 100],
            [['weapp_url', 'web_url'], 'string', 'max' => 225],
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
            'source' => Yii::t('app', '来源：1小猪电商 等'),
            'shop_api_key' => Yii::t('app', '对接的key'),
            'coupon_id' => Yii::t('app', '第三方券id'),
            'name' => Yii::t('app', '券名称'),
            'type' => Yii::t('app', '券类型 1：优惠券，2：赠送券 3 通用券 4 店铺券 5 兑换券 6代金券'),
            'face_money' => Yii::t('app', '券面额或者内容'),
            'limit_money' => Yii::t('app', '使用优惠券的订单金额下限 0：为不限定）'),
            'desc' => Yii::t('app', '使用说明'),
            'is_all_product' => Yii::t('app', '使用范围 0：全店通用，1：指定商品使用'),
            'time_type' => Yii::t('app', '时间类型 0：固定区间，1：固定时长'),
            'start_time' => Yii::t('app', '生效时间'),
            'end_time' => Yii::t('app', '过期时间'),
            'time_fixed' => Yii::t('app', '固定时长时文字描述'),
            'weapp_url' => Yii::t('app', '该优惠券小程序路径'),
            'web_url' => Yii::t('app', '该优惠券H5的路径'),
            'status' => Yii::t('app', '是否显示,默认 1，0隐藏，1显示'),
            'add_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }

    //获取来源
    public static function getType($type){
        switch ($type){
            case self::TYPE_SAIL:
                $type_name  = '优惠券';
                break;
            case self::TYPE_GIFT:
                $type_name  = '赠送券';
                break;
            case self::TYPE_ALL:
                $type_name  = '通用券';
                break;
            case self::TYPE_STORE:
                $type_name  = '店铺券';
                break;
            case self::TYPE_CHANGE:
                $type_name  = '兑换券';
                break;
            case self::TYPE_MONEY:
                $type_name  = '代金券';
                break;
            default:
                $type_name  = '';
                break;
        }
        return $type_name;
    }

    //添加优惠券
    public static function addCoupon($where, $data)
    {
        $model         = self::find()->where($where)->one();
        $oldAttributes = !empty($model);
        $model         = !empty($oldAttributes) ? $model : new ShopMaterialCoupon();
        $model->setAttributes($data);
        if (!$model->validate()) {
            throw new InvalidDataException(SUtils::modelError($model));
        }
        $oldAttributes ? $model->update() : $model->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_material_coupon_'.$data['corp_id']);
        return $model->id;
    }
}
