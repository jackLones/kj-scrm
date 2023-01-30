<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_doudian_category}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $shop_id 店铺id
 * @property int $cid 类目id
 * @property string $name 类目名称
 * @property int $level 类目级别：1，2，3级类目
 * @property int $parent_id 父类目id
 * @property int $is_leaf 是否是叶子节点 0 否 1是 
 * @property int $enable 是否有效 0 否 1是 
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopDoudianCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_doudian_category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'shop_id', 'level', 'parent_id', 'is_leaf', 'enable','cid'], 'integer'],
            [['add_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 225],
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
            'shop_id' => Yii::t('app', '店铺id'),
            'cid' => Yii::t('app', '类目id'),
            'name' => Yii::t('app', '类目名称'),
            'level' => Yii::t('app', '类目级别：1，2，3级类目'),
            'parent_id' => Yii::t('app', '父类目id'),
            'is_leaf' => Yii::t('app', '是否是叶子节点 0 否 1是 '),
            'enable' => Yii::t('app', '是否有效 0 否 1是 '),
            'add_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }

    public static function addCategory($where, $data)
    {
        $categoryModel    = self::find()->where($where)->one();
        $oldAttributes = !empty($categoryModel) ? clone $categoryModel : null;
        $orderModel    = !empty($oldAttributes) ? $oldAttributes : new ShopDoudianCategory();
        $orderModel->setAttributes($data);
        if (!$orderModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($orderModel));
        }
        !empty($oldAttributes) ? $orderModel->update() : $orderModel->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_doudian_category_'.$where['corp_id']);
        return $orderModel->id;
    }

}
