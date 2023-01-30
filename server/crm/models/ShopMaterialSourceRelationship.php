<?php

namespace app\models;

use app\components\InvalidParameterException;
use Yii;

/**
 * This is the model class for table "{{%shop_material_source_relationship}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $user_id 发送人id(导购id)
 * @property int $material_id 素材id
 * @property int $material_type 素材类型 1 product 商品，2 page 页面，3 coupon 券，
 * @property int $type 发送类型 1h5,2 小程序
 * @property int $review_count 浏览人数
 * @property int $channel 发送渠道 （1:好友，2:群）
 * @property int $chat_id 群聊id或者好友id
 * @property string|null $ext_json 发送内容快照
 * @property string $info 备注
 * @property string $short_flag 短地址标识
 * @property string $send_time 发送时间
 * @property string $update_time 更新时间
 */
class ShopMaterialSourceRelationship extends \yii\db\ActiveRecord
{
    /**
     * @val 1|商品
     */
    const MATERIAL_TYPE_PRODUCT = 1;
    /**
     * @val 2|页面
     */
    const MATERIAL_TYPE_PAGE = 2;
    /**
     * @val 3|优惠券
     */
    const MATERIAL_TYPE_COUPON = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_material_source_relationship}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'material_id', 'material_type', 'type', 'review_count', 'channel','chat_id'], 'integer'],
            [['ext_json'], 'string'],
            [['send_time', 'update_time'], 'safe'],
            [['short_flag'], 'string', 'max' => 10],
            [['info'], 'string', 'max' => 200],
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
            'material_type' => Yii::t('app', '素材类型 1 product 商品，2 page 页面，3 coupon 券，'),
            'type'          => Yii::t('app', '发送类型 1h5,2 小程序'),
            'review_count'  => Yii::t('app', '浏览人数'),
            'channel'       => Yii::t('app', '发送渠道 （1:好友，2:群）'),
            'chat_id'       => Yii::t('app', '群聊id或者好友id'),
            'ext_json'      => Yii::t('app', '发送内容快照'),
            'info'          => Yii::t('app', '备注'),
            'short_flag'    => Yii::t('app', '短地址标识'),
            'send_time'     => Yii::t('app', '发送时间'),
            'update_time'   => Yii::t('app', '更新时间')
        ];
    }

    //关联导购员工
    public function getUser()
    {
       return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

    //获取顾客或者群名称
    public static function getChatName($corpId, $type, $chatId)
    {
        $name = '';
        $where = ['corp_id' => $corpId, 'id' => $chatId];
        if ($type == 1) {
            $externalUserData = WorkExternalContact::findOne($where);
            if (!empty($externalUserData)) {
                $name = $externalUserData->name_convert ?: '暂无昵称';
            }
        }else{
            $name = WorkChat::getChatName($chatId);
        }
        return $name;
    }

}
