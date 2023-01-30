<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_third_order_product}}".
 *
 * @property int $id
 * @property int $third_order_id 关联 shop_third_order 主键
 * @property int $product_id  商品ID
 * @property int $sku_id  规格ID
 * @property string $name  商品名称
 * @property int $product_number  商品数量
 * @property float $price 价格
 * @property int $return_status 产品退款状态，0：未退款，1：部分退款，2：全部退完
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopThirdOrderProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_third_order_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['third_order_id', 'product_id', 'product_number', 'return_status', 'sku_id'], 'integer'],
            [['price'], 'number'],
            [['add_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('app', 'ID'),
            'third_order_id' => Yii::t('app', '关联 shop_third_order 主键 '),
            'product_id'     => Yii::t('app', ' 商品ID'),
            'sku_id'         => Yii::t('app', ' 规格id'),
            'name'           => Yii::t('app', ' 商品名称'),
            'product_number' => Yii::t('app', ' 商品数量'),
            'price'          => Yii::t('app', '价格'),
            'return_status'  => Yii::t('app', '产品退款状态，0：未退款，1：部分退款，2：全部退完'),
            'add_time'       => Yii::t('app', '添加时间'),
            'update_time'    => Yii::t('app', '更新时间'),
        ];
    }

    //查询订单
    public static function getData($where, $field)
    {
        $cacheKey = 'shop_third_order_product_' . json_encode($where) . '_' . json_encode($field);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($where, $field) {
            return self::find()->where($where)->select($field)->asArray()->all();
        }, null, new TagDependency(['tags' => 'shop_third_order_product']));
    }

    //添加订单产品数据
    public static function addThirdOrderProduct($where, $data)
    {
        $productModel  = ShopThirdOrderProduct::find()->where($where)->one();
        $oldAttributes = !empty($productModel) ? clone $productModel : null;
        $productModel  = !empty($oldAttributes) ? $oldAttributes : new ShopThirdOrderProduct();
        foreach ($data as $k => $v) {
            if (empty($v)) {
                unset($data[$k]);
            }
        }
        $productModel->setAttributes($data);
        if (!$productModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($productModel));
        }
        !empty($oldAttributes) ? $productModel->update() : $productModel->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_third_order_product');
        return $productModel->id;
    }
}
