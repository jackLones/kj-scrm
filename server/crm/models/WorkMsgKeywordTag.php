<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_msg_keyword_tag}}".
 *
 * @property int $id
 * @property int $corp_id 企业ID
 * @property int $keyword_id 推荐规则ID
 * @property string $tags 客户标签id（逗号分隔）
 * @property string $attachment_ids 内容引擎id集合
 * @property int $is_del 是否删除1是0否
 * @property int $add_time 添加时间
 *
 * @property WorkCorp $corp
 * @property WorkMsgKeywordAttachment $keyword
 */
class WorkMsgKeywordTag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_msg_keyword_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'keyword_id', 'is_del', 'add_time'], 'integer'],
            [['tags', 'attachment_ids'], 'string', 'max' => 5000],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['keyword_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgKeywordAttachment::className(), 'targetAttribute' => ['keyword_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '企业ID'),
            'keyword_id' => Yii::t('app', '推荐规则ID'),
            'tags' => Yii::t('app', '客户标签id（逗号分隔）'),
            'attachment_ids' => Yii::t('app', '内容引擎id集合'),
            'is_del' => Yii::t('app', '是否删除1是0否'),
            'add_time' => Yii::t('app', '添加时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorp()
    {
        return $this->hasOne(WorkCorp::className(), ['id' => 'corp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeyword()
    {
        return $this->hasOne(WorkMsgKeywordAttachment::className(), ['id' => 'keyword_id']);
    }
}
