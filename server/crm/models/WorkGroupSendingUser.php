<?php

namespace app\models;

use app\util\SUtils;
use Yii;

/**
 * This is the model class for table "{{%work_group_sending_user}}".
 *
 * @property int $id
 * @property int $user_id 员工ID
 * @property int $send_id 群发消息ID
 * @property int $times 当前员工确认次数
 * @property int $push_type 发送状态：0未发送1已发送
 * @property int $push_time 发送时间
 * @property string $msgid 群发消息ID
 * @property string $error_msg 错误信息
 * @property int $status 队列是否跑0未跑1已跑
 * @property int $create_time 创建时间
 *
 * @property WorkGroupSending $send
 * @property WorkUser $user
 */
class WorkGroupSendingUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_group_sending_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
	    return [
		    [['user_id', 'send_id', 'times', 'push_type', 'push_time', 'create_time', 'status'], 'integer'],
		    [['send_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkGroupSending::className(), 'targetAttribute' => ['send_id' => 'id']],
		    [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkUser::className(), 'targetAttribute' => ['user_id' => 'id']],
	    ];
    }

    /**
     * {@inheritdoc}
     */
	public function attributeLabels ()
	{
		return [
			'id'          => Yii::t('app', 'ID'),
			'user_id'     => Yii::t('app', '员工ID'),
			'send_id'     => Yii::t('app', '群发消息ID'),
			'times'       => Yii::t('app', '当前员工确认次数'),
			'push_type'   => Yii::t('app', '发送状态：0未发送1已发送'),
			'push_time'   => Yii::t('app', '发送时间'),
			'msgid'       => Yii::t('app', '群发消息ID'),
			'error_msg'   => Yii::t('app', '错误信息'),
			'status'      => Yii::t('app', '队列是否跑0未跑1已跑'),
			'create_time' => Yii::t('app', '创建时间'),
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSend()
    {
        return $this->hasOne(WorkGroupSending::className(), ['id' => 'send_id']);
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
    public function getUser()
    {
        return $this->hasOne(WorkUser::className(), ['id' => 'user_id']);
    }

	public static function add ($send_id, $user_id, $times)
	{
		$userStatistic              = new WorkGroupSendingUser();
		$userStatistic->create_time = time();
		$userStatistic->send_id     = $send_id;
		$userStatistic->user_id     = $user_id;
		$userStatistic->times       = $times;
		if (!$userStatistic->validate() || !$userStatistic->save()) {
			\Yii::error(SUtils::modelError($userStatistic), 'userStatistic');
		}
	}


}
