<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_work_moment_remind".
 *
 * @property string $id
 * @property string $user_id 成员ID
 * @property string $external_id 外部联系人ID
 * @property string $openid 外部非联系人openid
 * @property string $related_id 相关表id
 * @property string $moment_id 朋友圈id
 * @property int $status 状态 是否删除 0否 1是
 * @property int $type 类型 1 点赞 2评论
 * @property int $is_show 是否查看 0否 1是
 * @property string $create_time 创建时间
 */
class WorkMomentRemind extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_moment_remind}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remind_user_id', 'user_id', 'external_id', 'related_id', 'moment_id', 'moment_user_id', 'status', 'type', 'is_show'], 'integer'],
            [['create_time'], 'safe'],
            [['openid'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'remind_user_id' => Yii::t('app', '提醒用户id'),
            'user_id'     => Yii::t('app', '成员ID'),
            'external_id' => Yii::t('app', '外部联系人ID'),
            'openid'      => Yii::t('app', '外部非联系人openid'),
            'related_id'  => Yii::t('app', '相关表id'),
            'moment_id'   => Yii::t('app', '朋友圈id'),
            'moment_user_id' => Yii::t('app', '朋友圈所属成员id'),
            'status'      => Yii::t('app', '状态 是否删除 0否 1是'),
            'type'        => Yii::t('app', '类型 1 点赞 2评论'),
            'is_show'     => Yii::t('app', '是否查看 0否 1是'),
            'create_time' => Yii::t('app', '创建时间')
        ];
    }

    //添加消息提醒
    public function remindAdd($remind_user_id = 0, $user_id = 0, $external_id = 0, $related_id = 0, $moment_id = 0, $status = 0, $type = 1)
    {
        $remind = WorkMomentRemind::find()
            ->where(['remind_user_id' => $remind_user_id])
            ->andFilterWhere(['user_id' => $user_id])
            ->andFilterWhere(['external_id' => $external_id])
            ->andFilterWhere(['related_id' => $related_id])
            ->andFilterWhere(['moment_id' => $moment_id])
            ->andFilterWhere(['type' => $type])
            ->one();
        if($remind) {
            $remind->status = $status;
            $remind->save();
        } else {
            $WorkMoments = WorkMoments::findOne($moment_id);
            $WorkMomentRemind = new WorkMomentRemind();
            $WorkMomentRemind->remind_user_id = $remind_user_id;
            $WorkMomentRemind->moment_user_id = $WorkMoments->user_id;
            $WorkMomentRemind->user_id = $user_id;
            $WorkMomentRemind->external_id = $external_id;
            $WorkMomentRemind->related_id = $related_id;
            $WorkMomentRemind->moment_id = $moment_id;
            $WorkMomentRemind->type = $type;
            $WorkMomentRemind->save();
        }

        return true;
    }
}
