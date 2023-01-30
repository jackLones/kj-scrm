<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_msg_keyword_attachment_info}}".
 *
 * @property int $id
 * @property int $keyword_id 推荐规则ID
 * @property int $keyword_tag_id 推荐规则关联标签表ID
 * @property int $type 回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）
 * @property string $content 对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID
 * @property int $material_id 素材库ID
 * @property int $attachment_id 附件id
 * @property string $title 图文消息的标题
 * @property string $digest 图文消息的摘要
 * @property string $author 图文消息的作者
 * @property int $show_cover 是否显示封面，0为不显示，1为显示
 * @property string $cover_url 封面图片的URL
 * @property string $content_url 正文的URL
 * @property string $source_url 原文的URL，若置空则无查看原文入口
 * @property int $status 是否开启，0代表未开启，1代表开启
 * @property int $is_use 是否是自定义
 * @property int $is_sync 是否同步文件柜
 * @property int $attach_id 同步文件柜的id
 * @property string $create_time 创建时间
 * @property string $appid 小程序的appid
 * @property string $pagepath 小程序的页面路径
 *
 * @property WorkMsgKeywordAttachment $keyword
 * @property WorkMsgKeywordTag $keywordTag
 * @property Material $material
 */
class WorkMsgKeywordAttachmentInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_msg_keyword_attachment_info}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keyword_id', 'keyword_tag_id', 'type', 'material_id', 'attachment_id', 'show_cover', 'status', 'is_use', 'is_sync', 'attach_id'], 'integer'],
            [['type'], 'required'],
            [['content', 'cover_url', 'content_url', 'source_url'], 'string'],
            [['create_time'], 'safe'],
            [['title'], 'string', 'max' => 64],
            [['digest', 'appid', 'pagepath'], 'string', 'max' => 255],
            [['author'], 'string', 'max' => 16],
            [['keyword_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkMsgKeywordAttachment::className(), 'targetAttribute' => ['keyword_id' => 'id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Material::className(), 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'keyword_id' => Yii::t('app', '推荐规则ID'),
            'keyword_tag_id' => Yii::t('app', '推荐规则关联标签表ID'),
            'type' => Yii::t('app', '回复类型，1：文本（text）、2：图片（img）、3：语音（voice）、4：视频（video）、5：图文（news）'),
            'content' => Yii::t('app', '对于文本类型，content是文本内容，对于图文、图片、语音、视频类型，content是mediaID'),
            'material_id' => Yii::t('app', '素材库ID'),
            'attachment_id' => Yii::t('app', '附件id'),
            'title' => Yii::t('app', '图文消息的标题'),
            'digest' => Yii::t('app', '图文消息的摘要'),
            'author' => Yii::t('app', '图文消息的作者'),
            'show_cover' => Yii::t('app', '是否显示封面，0为不显示，1为显示'),
            'cover_url' => Yii::t('app', '封面图片的URL'),
            'content_url' => Yii::t('app', '正文的URL'),
            'source_url' => Yii::t('app', '原文的URL，若置空则无查看原文入口'),
            'status' => Yii::t('app', '是否开启，0代表未开启，1代表开启'),
            'is_use' => Yii::t('app', '是否是自定义'),
            'is_sync' => Yii::t('app', '是否同步文件柜'),
            'attach_id' => Yii::t('app', '同步文件柜的id'),
            'create_time' => Yii::t('app', '创建时间'),
            'appid' => Yii::t('app', '小程序的appid'),
            'pagepath' => Yii::t('app', '小程序的页面路径'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKeyword()
    {
        return $this->hasOne(WorkMsgKeywordAttachment::className(), ['id' => 'keyword_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::className(), ['id' => 'material_id']);
    }
}
