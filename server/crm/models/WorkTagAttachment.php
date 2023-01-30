<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_tag_attachment}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $tag_id 内容标签ID
 * @property int $attachment_id 内容ID
 * @property int $status 0不显示1显示
 * @property string $update_time 更新时间
 * @property string $add_time 创建时间
 *
 * @property Attachment $attachment
 * @property WorkCorp $corp
 * @property WorkTag $tag
 */
class WorkTagAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_tag_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'tag_id', 'attachment_id', 'status'], 'integer'],
            [['update_time', 'add_time'], 'safe'],
            [['attachment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attachment::className(), 'targetAttribute' => ['attachment_id' => 'id']],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'corp_id' => Yii::t('app', '授权的企业ID'),
            'tag_id' => Yii::t('app', '内容标签ID'),
            'attachment_id' => Yii::t('app', '内容ID'),
            'status' => Yii::t('app', '0不显示1显示'),
            'update_time' => Yii::t('app', '更新时间'),
            'add_time' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachment()
    {
        return $this->hasOne(Attachment::className(), ['id' => 'attachment_id']);
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
    public function getTag()
    {
        return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
    }

	/**
	 * @param $id
	 *
	 * @return array
	 *
	 */
	public static function getTagName ($id)
	{
		$tagAttachment = static::find()->alias('w');
		$tagAttachment = $tagAttachment->leftJoin('{{%work_tag}} t', '`t`.`id` = `w`.`tag_id`')->andWhere(['t.is_del' => 0, 'w.status' => 1, 'w.attachment_id' => $id]);
		$tagAttachment = $tagAttachment->select('t.id, t.tagname');
		$tagAttachment = $tagAttachment->asArray()->all();
		$tagName       = [];
		foreach ($tagAttachment as $k => $v) {
			$tagName[] = ['id' => $v['id'], 'tagname' => $v['tagname']];
		}

		return $tagName;
	}
}
