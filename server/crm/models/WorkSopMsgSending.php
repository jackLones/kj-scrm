<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_sop_msg_sending}}".
 *
 * @property int $id
 * @property int $corp_id 企业ID
 * @property int $is_chat 是否群SOP消息1是0否
 * @property int $sop_id 规则id
 * @property int $sop_time_id 规则时间id
 * @property int $user_id 成员ID
 * @property int $external_id 外部联系人ID
 * @property int $send_time 预发送时间
 * @property string $content 发送内容
 * @property int $queue_id 队列id
 * @property int $status 发送状态 0未发送 1已发送 2发送失败
 * @property int $push_time 成功发送时间
 * @property string $error_msg 错误信息
 * @property int $error_code 错误码
 * @property int $is_over 是否完成1是0否
 * @property int $over_time 完成时间
 * @property int $is_del 删除状态 0 未删除 1 已删除
 * @property string $create_time 创建时间
 * @property string $update_time 更新时间
 *
 * @property WorkCorp $corp
 * @property WorkSop $sop
 * @property WorkSopTime $sopTime
 */
class WorkSopMsgSending extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_sop_msg_sending}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id', 'is_chat', 'sop_id', 'sop_time_id', 'user_id', 'external_id', 'send_time', 'queue_id', 'status', 'push_time', 'error_code', 'is_over', 'over_time', 'is_del'], 'integer'],
            [['content'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['error_msg'], 'string', 'max' => 255],
            [['corp_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkCorp::className(), 'targetAttribute' => ['corp_id' => 'id']],
            [['sop_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkSop::className(), 'targetAttribute' => ['sop_id' => 'id']],
            [['sop_time_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkSopTime::className(), 'targetAttribute' => ['sop_time_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => Yii::t('app', 'ID'),
			'corp_id'     => Yii::t('app', '企业ID'),
			'is_chat'     => Yii::t('app', '是否群SOP消息1是0否'),
			'sop_id'      => Yii::t('app', '规则id'),
			'sop_time_id' => Yii::t('app', '规则时间id'),
			'user_id'     => Yii::t('app', '成员ID'),
			'external_id' => Yii::t('app', '外部联系人ID'),
			'send_time'   => Yii::t('app', '预发送时间'),
			'content'     => Yii::t('app', '发送内容'),
			'queue_id'    => Yii::t('app', '队列id'),
			'status'      => Yii::t('app', '发送状态 0未发送 1已发送 2发送失败'),
			'push_time'   => Yii::t('app', '成功发送时间'),
			'error_msg'   => Yii::t('app', '错误信息'),
			'error_code'  => Yii::t('app', '错误码'),
			'is_over'     => Yii::t('app', '是否完成1是0否'),
			'over_time'   => Yii::t('app', '完成时间'),
			'is_del'      => Yii::t('app', '删除状态 0 未删除 1 已删除'),
			'create_time' => Yii::t('app', '创建时间'),
			'update_time' => Yii::t('app', '更新时间'),
		];
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
    public function getSop()
    {
        return $this->hasOne(WorkSop::className(), ['id' => 'sop_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSopTime()
    {
        return $this->hasOne(WorkSopTime::className(), ['id' => 'sop_time_id']);
    }
}
