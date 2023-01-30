<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_material_product}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $product_id 商品id
 * @property int $source 来源：1小猪电商 等
 * @property int $shop_api_key 对接的key
 * @property int $group_id 分组id
 * @property int $cate_id 分类id
 * @property string $name 商品名称
 * @property string $code 商品编码
 * @property int $type 商品类型：0普通，1拼团，2积分，3秒杀，4砍价，5限时活动
 * @property int $stock 库存
 * @property int $sales 销量
 * @property float $original_price  原价
 * @property float $original_end_price 默认0，原价为区间时此为区间最大值
 * @property float $price 售价
 * @property float $end_price 默认0，售价为区间时此为区间最大值
 * @property string $image 商品主图地址
 * @property string $weapp_url 该商品小程序路径
 * @property string $web_url 该商品 H5 的路径
 * @property string|null $recommend_remark 商品推荐语
 * @property int $status 是否显示,默认 1，0隐藏，1显示
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopMaterialProduct extends \yii\db\ActiveRecord
{
    /**
     * @var 1 小猪电商
     */
    const SOURCE_PIG = 1;

    /**
     * @val 1|正常
     */
    const STATUS_SHOW = 1;
    /**
     * @val  0|下架
     */
    const STATUS_HIDE = 0;

    /**
     * @val  0|普通
     */
    const TYPE_GENERAL = 0;
    /**
     * @val  1|团购
     */
    const TYPE_GROUP_BUY = 1;
    /**
     * @val  2|积分
     */
    const TYPE_POINT = 2;
    /**
     * @val  3|秒杀
     */
    const TYPE_SEC_KILL = 3;
    /**
     * @val  4|砍价
     */
    const TYPE_BARGAINING = 4;
    /**
     * @val  5|限时活动
     */
    const TYPE_TIME_LIMIT = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'product_id', 'source', 'group_id', 'cate_id', 'type', 'stock', 'sales', 'status'], 'integer'],
            [['original_price', 'original_end_price', 'price', 'end_price'], 'number'],
            [['recommend_remark'], 'string'],
            [['add_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 200],
            [['code', 'shop_api_key'], 'string', 'max' => 100],
            [['image', 'weapp_url', 'web_url'], 'string', 'max' => 225],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => Yii::t('app', 'ID'),
            'corp_id'            => Yii::t('app', '授权的企业ID'),
            'product_id'         => Yii::t('app', '第三方商品id'),
            'shop_api_key'       => Yii::t('app', '对接的key'),
            'source'             => Yii::t('app', '来源：1小猪电商 等'),
            'group_id'           => Yii::t('app', '分组id'),
            'cate_id'            => Yii::t('app', '分类id'),
            'name'               => Yii::t('app', '商品名称'),
            'code'               => Yii::t('app', '商品编码'),
            'type'               => Yii::t('app', '商品类型：0普通，1拼团，2积分，3秒杀，4砍价，5限时活动'),
            'stock'              => Yii::t('app', '库存'),
            'sales'              => Yii::t('app', '销量'),
            'original_price'     => Yii::t('app', '原价'),
            'original_end_price' => Yii::t('app', '默认0，原价为区间时此为区间最大值'),
            'price'              => Yii::t('app', '售价'),
            'end_price'          => Yii::t('app', '默认0，售价为区间时此为区间最大值'),
            'image'              => Yii::t('app', '商品主图地址'),
            'weapp_url'          => Yii::t('app', '该商品小程序路径'),
            'web_url'            => Yii::t('app', '该商品 H5 的路径'),
            'recommend_remark'   => Yii::t('app', '商品推荐语'),
            'status'             => Yii::t('app', '是否显示,默认 1，0隐藏，1显示'),
            'add_time'           => Yii::t('app', '添加时间'),
            'update_time'        => Yii::t('app', '更新时间'),
        ];
    }

    //关联分组
    public function getGroup()
    {
        return $this->hasOne(ShopMaterialProductGroup::className(), ['id' => 'group_id']);
    }

    //关联收藏
    public function getCollect()
    {
        return $this->hasMany(ShopMaterialCollect::className(), ['material_id' => 'id']);
    }

    //获取来源
    public static function getSource($source)
    {
        switch ($source) {
            case self::SOURCE_PIG:
                $source_name = '小猪电商';
                break;
            default:
                $source_name = '未知平台';
                break;
        }
        return $source_name;
    }

    //获取来源
    public static function getType($type)
    {
        switch ($type) {
            case self::TYPE_GENERAL:
                $type_name = '普通';
                break;
            case self::TYPE_GROUP_BUY:
                $type_name = '拼团';
                break;
            case self::TYPE_POINT:
                $type_name = '积分';
                break;
            case self::TYPE_SEC_KILL:
                $type_name = '秒杀';
                break;
            case self::TYPE_BARGAINING:
                $type_name = '砍价';
                break;
            case self::TYPE_TIME_LIMIT:
                $type_name = '限时活动';
                break;
            default:
                $type_name = '';
                break;
        }
        return $type_name;
    }

    //添加商品
    public static function addProduct($where, $data)
    {
        $model         = self::find()->where($where)->one();
        $oldAttributes = !empty($model);
        $model         = !empty($oldAttributes) ? $model : new ShopMaterialProduct();
        $model->setAttributes($data);
        if (!$model->validate()) {
            throw new InvalidDataException(SUtils::modelError($model));
        }
        !empty($oldAttributes) ? $model->update() : $model->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_material_product_' . $data['corp_id']);
        return $model->id;
    }
}
