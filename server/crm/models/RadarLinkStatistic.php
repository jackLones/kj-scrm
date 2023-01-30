<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%radar_link_statistic}}".
 *
 * @property int $id
 * @property int $radar_link_id 雷达链接ID
 * @property int $corp_id 企业ID
 * @property int $user_id 成员ID
 * @property int $external_id 外部联系人ID
 * @property int $chat_id 客户群ID
 * @property string $openid 用户openid
 * @property string $open_time 打开时间
 * @property string $leave_time 离开时间
 * @property int $clicks 打开次数
 * @property string $created_at 创建时间
 *
 * @property WorkChat $chat
 * @property WorkCorp $corp
 * @property WorkExternalContact $external
 * @property RadarLink $radarLink
 * @property WorkUser $user
 */
class RadarLinkStatistic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%radar_link_statistic}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['radar_link_id'], 'required'],
            [['radar_link_id', 'corp_id', 'user_id', 'external_id', 'chat_id', 'clicks'], 'integer'],
            [['open_time', 'leave_time', 'created_at'], 'safe'],
            [['openid'], 'string', 'max' => 64],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkChat::className(), 'targetAttribute' => ['chat_id' => 'id']],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkExternalContact::className(), 'targetAttribute' => ['external_id' => 'id']],
            [['radar_link_id'], 'exist', 'skipOnError' => true, 'targetClass' => RadarLink::className(), 'targetAttribute' => ['radar_link_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'radar_link_id' => Yii::t('app', '雷达链接ID'),
            'corp_id' => Yii::t('app', '企业ID'),
            'user_id' => Yii::t('app', '成员ID'),
            'external_id' => Yii::t('app', '外部联系人ID'),
            'chat_id' => Yii::t('app', '客户群ID'),
            'openid' => Yii::t('app', '用户openid'),
            'open_time' => Yii::t('app', '打开时间'),
            'leave_time' => Yii::t('app', '离开时间'),
            'clicks' => Yii::t('app', '打开次数'),
            'created_at' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(WorkChat::className(), ['id' => 'chat_id']);
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
    public function getExternal()
    {
        return $this->hasOne(WorkExternalContact::className(), ['id' => 'external_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRadarLink()
    {
        return $this->hasOne(RadarLink::className(), ['id' => 'radar_link_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }
}
