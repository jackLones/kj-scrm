<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%custom_field_chat}}".
 *
 * @property int $id
 * @property int $uid 商户的uid
 * @property int $fieldid 属性字段表id
 * @property int $time 时间
 * @property int $status 0关闭，1开启
 *
 * @property User $u
 * @property CustomField $field
 */
class CustomFieldChat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%custom_field_chat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'fieldid', 'time', 'status'], 'integer'],
            [['time'], 'required'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'uid']],
            [['fieldid'], 'exist', 'skipOnError' => true, 'targetClass' => CustomField::className(), 'targetAttribute' => ['fieldid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', '商户的uid'),
            'fieldid' => Yii::t('app', '属性字段表id'),
            'time' => Yii::t('app', '时间'),
            'status' => Yii::t('app', '0关闭，1开启'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(User::className(), ['uid' => 'uid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(CustomField::className(), ['id' => 'fieldid']);
    }
}
