<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_public_activity_tier}}".
 *
 * @property int $id
 * @property int $activity_id 活动id
 * @property int $parent_id 上级id
 * @property int $parent 任务宝参与上级id一对一
 * @property string $parent_fans 任务宝参与上级id
 * @property int $fans_id 任务宝参与id
 * @property string $level 级别
 * @property int $create_time
 *
 * @property WorkPublicActivity $activity
 * @property WorkPublicActivityFansUser $fans
 */
class WorkPublicActivityTier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_public_activity_tier}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['activity_id', 'parent_id', 'parent', 'fans_id', 'create_time'], 'integer'],
            [['parent_fans', 'level'], 'string'],
            [['activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivity::className(), 'targetAttribute' => ['activity_id' => 'id']],
            [['fans_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkPublicActivityFansUser::className(), 'targetAttribute' => ['fans_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'activity_id' => Yii::t('app', '活动id'),
            'parent_id' => Yii::t('app', '上级id'),
            'parent' => Yii::t('app', '任务宝参与上级id一对一'),
            'parent_fans' => Yii::t('app', '任务宝参与上级id'),
            'fans_id' => Yii::t('app', '任务宝参与id'),
            'level' => Yii::t('app', '级别'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivity()
    {
        return $this->hasOne(WorkPublicActivity::className(), ['id' => 'activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFans()
    {
        return $this->hasOne(WorkPublicActivityFansUser::className(), ['id' => 'fans_id']);
    }
}
