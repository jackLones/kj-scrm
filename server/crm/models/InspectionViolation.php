<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pig_inspection_violation".
 *
 * @property int $id
 * @property int $corp_id 企业id
 * @property int $user_id 质检人id
 * @property int $quality_id 质检对象id
 * @property int $work_msg_audit_info_id 会话记录id
 * @property int $to_user_id 用户id  群聊时为0
 * @property string $roomid 群聊id 如果是单聊则为空
 * @property string $content 批注
 * @property string $content_classify 批注样式格式
 * @property int $msg_type 会话类型 0 客户 1群聊
 * @property int $status 存留字段，用于质检用户未出现违规情况，0未违规 1违规
 * @property string $create_time 创建时间
 * @property int $is_delete 是否删除 0否 1是
 */
class InspectionViolation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%inspection_violation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'user_id', 'quality_id', 'work_msg_audit_info_id', 'to_user_id', 'msg_type', 'status', 'is_delete'], 'integer'],
            [['content_classify'], 'string'],
            [['create_time'], 'safe'],
            [['roomid'], 'string', 'max' => 64],
            [['content'], 'string', 'max' => 255],
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
            'user_id' => Yii::t('app', '质检人id'),
            'quality_id' => Yii::t('app', '质检对象id'),
            'work_msg_audit_info_id' => Yii::t('app', '会话记录id'),
            'to_user_id' => Yii::t('app', '用户id  群聊时为0'),
            'roomid' => Yii::t('app', '群聊id 如果是单聊则为空'),
            'content' => Yii::t('app', '批注'),
            'content_classify' => Yii::t('app', '批注样式格式'),
            'msg_type' => Yii::t('app', '会话类型 0 客户 1群聊'),
            'status' => Yii::t('app', '是否提交 0 未提交 1 已提交'),
            'create_time' => Yii::t('app', '创建时间'),
            'is_delete' => Yii::t('app', '是否删除 0否 1是'),
        ];
    }
}
