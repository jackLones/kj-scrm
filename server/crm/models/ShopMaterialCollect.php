<?php

namespace app\models;

use app\components\InvalidDataException;
use Yii;

/**
 * This is the model class for table "{{%shop_material_collect}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $user_id 收藏人id(导购id)
 * @property int $material_id 素材id
 * @property int $material_type 素材类型 1 product商品，2 page页面，3 coupon券
 * @property string $add_time 收藏时间
 */
class ShopMaterialCollect extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_collect}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'material_id', 'material_type'], 'integer'],
            [['add_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'corp_id'       => Yii::t('app', '授权的企业ID'),
            'user_id'       => Yii::t('app', '发送人id(导购id)'),
            'material_id'   => Yii::t('app', '素材id'),
            'material_type' => Yii::t('app', '素材类型 1 product商品，2 page页面，3 coupon券'),
            'add_time'      => Yii::t('app', '发送时间'),
        ];
    }

    //关联商品表
    public  function getProduct(){
        return $this->hasOne(ShopMaterialProduct::className(), ['id' => 'material_id']);
    }

    //保存收藏
    public static function saveData($saveData){
        $oldData  = ShopMaterialCollect::findOne($saveData);
        if (empty($oldData)) {
            $shopMaterialProduct = new  ShopMaterialCollect();
            $shopMaterialProduct->setAttributes($saveData);
            $re = $shopMaterialProduct->save();
            if ($re) {
                return true;
            } else {
                throw new InvalidDataException('收藏失败！请重试！');
            }
        }
        return true;
    }
}
