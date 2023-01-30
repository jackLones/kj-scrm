<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%work_import_customer_msg_send}}".
 *
 * @property int $id
 * @property int $corp_id 企业微信id
 * @property int $import_id 导入表id
 * @property int $user_id 提醒成员ID
 * @property int $add_num 分配客户数
 * @property int $status 发送状态 0未发送 1已发送 2发送失败
 * @property int $time 发送时间
 * @property int $error_code 错误码
 * @property string $error_msg 错误信息
 */
class WorkImportCustomerMsgSend extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%work_import_customer_msg_send}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corp_id'], 'required'],
            [['corp_id', 'import_id', 'user_id', 'add_num', 'status', 'time', 'error_code'], 'integer'],
            [['error_msg'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
	    return [
		    'id'         => 'ID',
		    'corp_id'    => 'Corp ID',
		    'import_id'  => 'Import ID',
		    'user_id'    => 'User ID',
		    'add_num'    => 'Add Num',
		    'status'     => 'Status',
		    'time'       => 'Time',
		    'error_code' => 'Error Code',
		    'error_msg'  => 'Error Msg',
	    ];
    }
}
