<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_moment_edit}}".
 *
 * @property int $id
 * @property int $corp_id 企业id
 * @property int $user_id 员工id
 * @property int $status 1完成2正在编辑
 * @property string $info 保存编辑内容
 * @property string $create_time
 *
 * @property WorkUser $user
 * @property WorkCorp $corp
 */
class WorkMomentEdit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_moment_edit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'status'], 'integer'],
            [['info'], 'string'],
            [['create_time'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '企业id'),
            'user_id' => Yii::t('app', '员工id'),
            'status' => Yii::t('app', '1完成2正在编辑'),
            'info' => Yii::t('app', '保存编辑内容'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }
}
