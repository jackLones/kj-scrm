<?php

use yii\db\Migration;

/**
 * Class m200729_111907_add_table_work_group_sending_user
 */
class m200729_111907_add_table_work_group_sending_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_sending_user}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'user_id'     => $this->integer(11)->unsigned()->comment('员工ID'),
			'send_id'     => $this->integer(11)->unsigned()->comment('群发消息ID'),
			'times'       => $this->integer(11)->unsigned()->comment('当前员工确认次数'),
			'push_type'   => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('发送状态：0未发送1已发送'),
			'push_time'   => $this->integer(11)->unsigned()->comment('发送时间'),
			'msgid'       => $this->text()->comment('群发消息ID'),
			'error_msg'   => $this->text()->comment('错误信息'),
			'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('队列是否跑0未跑1已跑'),
			'create_time' => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群发成员确认信息表\'');

		$this->addForeignKey('KEY_WORK_GROUP_SENDING_USER_USER_ID', '{{%work_group_sending_user}}', 'user_id', '{{%work_user}}', 'id');
		$this->addForeignKey('KEY_WORK_GROUP_SENDING_USER_SEND_ID', '{{%work_group_sending_user}}', 'send_id', '{{%work_group_sending}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_111907_add_table_work_group_sending_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_111907_add_table_work_group_sending_user cannot be reverted.\n";

        return false;
    }
    */
}
