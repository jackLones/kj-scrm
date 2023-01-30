<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%shop_started_source_relationship}}".
 *
 * @property int $id
 * @property int $user_id  发送人ID
 * @property string $ext_json json 存储依赖关系
 * @property string $info 备注
 * @property int $channel 发送渠道，0未知，1：好友ID，2：群ID
 * @property int $send_from 发送介质，0未知，1：小程序，2：H5
 * @property string $add_time 添加时间
 * @property string $send_time 更新时间
 * @property string $update_time 更新时间
 */
class ShopStartedSourceRelationship extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%shop_started_source_relationship}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'channel', 'send_from'], 'integer'],
            [['ext_json'], 'required'],
            [['ext_json'], 'string'],
            [['add_time', 'send_time', 'update_time'], 'safe'],
            [['info'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'user_id'     => Yii::t('app', ' 发送人ID'),
            'ext_json'    => Yii::t('app', 'json 存储依赖关系'),
            'info'        => Yii::t('app', '备注'),
            'channel'     => Yii::t('app', '发送渠道，0未知，1：好友ID，2：群ID'),
            'send_from'   => Yii::t('app', '发送介质，0未知，1：小程序，2：H5'),
            'add_time'    => Yii::t('app', '添加时间'),
            'send_time'   => Yii::t('app', '更新时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }
}
