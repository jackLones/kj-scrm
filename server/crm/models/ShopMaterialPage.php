<?php

namespace app\models;

use app\components\InvalidDataException;
use app\util\SUtils;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%shop_material_page}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $shop_api_key 对接的key
 * @property int $source 来源：1小猪电商 等
 * @property int $page_id 第三方页面id
 * @property string $title 页面标题
 * @property string $image 页面封面
 * @property string|null $desc 页面描述
 * @property string $weapp_url 该页面小程序路径
 * @property string $web_url 该页面H5的路径
 * @property int $status 是否显示,默认 1，0隐藏，1显示
 * @property string $add_time 添加时间
 * @property string $update_time 更新时间
 */
class ShopMaterialPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_page}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'source', 'page_id', 'status'], 'integer'],
            [['desc'], 'string'],
            [['add_time', 'update_time'], 'safe'],
            [['title'], 'string', 'max' => 200],
            [['image', 'shop_api_key'], 'string', 'max' => 100],
            [['weapp_url', 'web_url'], 'string', 'max' => 225],
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
            'source'       => Yii::t('app', '来源：1小猪电商 等'),
            'page_id'      => Yii::t('app', '第三方页面id'),
            'title'        => Yii::t('app', '页面标题'),
            'image'        => Yii::t('app', '页面封面'),
            'desc'         => Yii::t('app', '页面描述'),
            'weapp_url'    => Yii::t('app', '该页面小程序路径'),
            'web_url'      => Yii::t('app', '该页面H5的路径'),
            'status'       => Yii::t('app', '是否显示,默认 1，0隐藏，1显示'),
            'add_time'     => Yii::t('app', '添加时间'),
            'update_time'  => Yii::t('app', '更新时间'),
        ];
    }


    //添加页面
    public static function addPage($where, $data)
    {
        $model         = self::find()->where($where)->one();
        $oldAttributes = !empty($model);
        $model         = $oldAttributes ? $model : new ShopMaterialPage();
        $model->setAttributes($data);
        if (!$model->validate()) {
            throw new InvalidDataException(SUtils::modelError($model));
        }
        !empty($oldAttributes) ? $model->update() : $model->save();
        TagDependency::invalidate(\Yii::$app->cache, 'shop_material_page_'.$data['corp_id']);
        return $model->id;
    }
}
