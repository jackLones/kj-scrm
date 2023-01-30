<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%dialout_key}}".
 *
 * @property int $id
 * @property string $api_type
 * @property string $api_key 平台分给的key
 * @property string $remark 描述
 */
class DialoutKey extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%dialout_key}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['api_type', 'api_key'], 'required'],
            [['api_type', 'api_key'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'api_type' => Yii::t('app', 'Api Type'),
            'api_key' => Yii::t('app', '平台分给的key'),
            'remark' => Yii::t('app', '描述'),
        ];
    }
}
