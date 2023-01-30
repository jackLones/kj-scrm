<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_material_product_group}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $shop_api_key 对接的key
 * @property int $group_id 第三方分组id
 * @property int $source 来源：1小猪电商 等
 * @property string $name 分组名称
 * @property int $sort 排序
 * @property int $status 是否显示,默认 1，0隐藏，1显示
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopMaterialProductGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_product_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'source', 'sort', 'status', 'group_id'], 'integer'],
            [['add_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['shop_api_key'], 'string', 'max' => 100],
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
            'shop_api_key' => Yii::t('app', '对接的key'),
            'group_id'     => Yii::t('app', '第三方分组id'),
            'source'       => Yii::t('app', '来源：1小猪电商 等'),
            'name'         => Yii::t('app', '分组名称'),
            'sort'         => Yii::t('app', '排序'),
            'status'       => Yii::t('app', '是否显示,默认 1，0隐藏，1显示'),
            'add_time'     => Yii::t('app', '添加时间'),
            'update_time'  => Yii::t('app', '更新时间'),
        ];
    }

    //添加分组数据
    public static function addProductGroup($where,$data)
    {
        $groupModel = self::find()->where($where)->one();
        $oldAttributes = !empty($groupModel);
        $groupModel = $oldAttributes ? $groupModel : new ShopMaterialProductGroup();
        $groupModel->setAttributes($data);
        if (!$groupModel->validate()) {
            throw new InvalidDataException(SUtils::modelError($groupModel));
        }
        $oldAttributes ? $groupModel->update() : $groupModel->save();
        if(!empty($where['corp_id'])){
            TagDependency::invalidate(\Yii::$app->cache,'shop_material_product_group_'.$where['corp_id']);
        }else{
            TagDependency::invalidate(\Yii::$app->cache,'shop_material_product_group');
        }
        return $groupModel->id;
    }

    //获取商品分组
    public static function getGroupList($corp_id, $where)
    {
        $cacheKey = 'shop_material_product_group_' . $corp_id . '_' . json_encode($where);
        return \Yii::$app->cache->getOrSet($cacheKey, function () use ($corp_id, $where) {
            return self::find()->select('id,name')->where(['corp_id' => $corp_id, 'status' => 1])->andWhere($where)->asArray()->all();
        }, null, new TagDependency(['tags' => ['shop_material_product_group_' . $corp_id, 'shop_material_product_group']]));
    }
}
