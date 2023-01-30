<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "pig_wechat_menus_keyword_relation".
 *
 * @property int $id
 * @property string $appid 微信公众号appid
 * @property string $menu_id 微信微信菜单ID
 * @property string $keyword 菜单KEY值
 * @property int $reply_info_id 回复关联ID
 * @property string $create_time
 * @property string $update_time
 */
class WechatMenusKeywordRelation extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%wechat_menus_keyword_relation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appid'], 'string', 'max' => 255],
            [['menu_id'], 'integer'],
            [['keyword'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appid' => '微信公众号appid',
            'menu_id' => '微信菜单ID',
            'keyword' => '菜单KEY值'
        ];
    }

    public function behaviors(){
        return [
            [
                'class'=>TimestampBehavior::class,
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time','update_time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
                ],
                'value' => new Expression('NOW()'),
            ]
        ];
    }
}
