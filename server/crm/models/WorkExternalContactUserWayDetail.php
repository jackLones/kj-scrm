<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_external_contact_user_way_detail}}".
 *
 * @property int $id
 * @property int $way_id 群聊活码ID
 * @property int $chat_id 群聊id
 * @property int $way_list_id 活码群聊对应表ID
 * @property int $user_id 企业发送人
 * @property int $external_id 外部接入人
 * @property int $create_time 创建时间
 *
 * @property WorkChat $chat
 * @property WorkExternalContactFollowUser $external
 * @property WorkUser $user
 * @property WorkChatContactWay $way
 */
class WorkExternalContactUserWayDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_external_contact_user_way_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['way_id', 'chat_id', 'way_list_id', 'user_id', 'external_id', 'create_time'], 'integer'],
            [['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContactFollowUser::className(), 'targetAttribute' => ['external_id' => 'id']],
            [['way_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChatWayList::className(), 'targetAttribute' => ['way_list_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['way_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChatContactWay::className(), 'targetAttribute' => ['way_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'way_id' => Yii::t('app', '群聊活码ID'),
            'chat_id' => Yii::t('app', '群聊id'),
            'way_list_id' => Yii::t('app', '活码群聊对应表ID'),
            'user_id' => Yii::t('app', '企业发送人'),
            'external_id' => Yii::t('app', '外部接入人'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(WorkExternalContactFollowUser::className(), ['id' => 'external_id']);
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
    public function getWay()
    {
        return $this->hasOne(WorkChatContactWay::className(), ['id' => 'way_id']);
    }


    public static function insertData($data)
    {
	    try {
		    $detail = new WorkExternalContactUserWayDetail();
		    $detail->way_list_id = $data['way_list_id'];
		    $detail->way_id = $data['way_id'];
		    $detail->chat_id = $data['chat_id'];
		    $detail->user_id = $data['user_id'];
		    $detail->external_id = $data['external_id'];
		    $detail->create_time = time();
		    $detail->save();
		    \Yii::error($data, 'WorkExternalContactUserWayDetail');
	    }catch (\Exception $e){
		    \Yii::error($data, 'WorkExternalContactUserWayDetail');
		    \Yii::error($e->getMessage(), 'WorkExternalContactUserWayDetail');
	    }

    }



}
