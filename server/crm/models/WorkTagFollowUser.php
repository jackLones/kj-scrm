<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_tag_follow_user}}".
 *
 * @property int $id
 * @property int $corp_id 授权的企业ID
 * @property int $tag_id 授权的企业的标签ID
 * @property int $status 0不显示1显示
 * @property int $follow_user_id 外部联系人对应的ID
 * @property int $success 0未跑完1已跑完
 * @property int $add_time 添加时间
 * @property int $update_time 修改时间
 *
 * @property WorkExternalContactFollowUser $followUser
 * @property WorkTag $tag
 */
class WorkTagFollowUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_tag_follow_user}}';
    }

    /**
     * {@inheritdoc}
     */
	public function rules ()
	{
		return [
			[['tag_id', 'status', 'follow_user_id', 'corp_id','success'], 'integer'],
			[['follow_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContactFollowUser::className(), 'targetAttribute' => ['follow_user_id' => 'id']],
			[['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkTag::className(), 'targetAttribute' => ['tag_id' => 'id']],
		];
	}

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'             => Yii::t('app', 'ID'),
			'corp_id'        => Yii::t('app', '授权的企业ID'),
			'tag_id'         => Yii::t('app', '授权的企业的标签ID'),
			'status'         => Yii::t('app', '0不显示1显示'),
			'follow_user_id' => Yii::t('app', '外部联系人对应的ID'),
			'success'        => Yii::t('app', '0未跑完1已跑完'),
			'add_time'       => Yii::t('app', '添加时间'),
			'update_time'    => Yii::t('app', '修改时间'),
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowUser()
    {
        return $this->hasOne(WorkExternalContactFollowUser::className(), ['id' => 'follow_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(WorkTag::className(), ['id' => 'tag_id']);
    }

	/**
	 *
	 * @return object|\yii\db\Connection|null
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getDb ()
	{
		return Yii::$app->get('mdb');
	}

}
