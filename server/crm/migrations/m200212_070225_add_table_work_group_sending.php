<?php

use yii\db\Migration;

/**
 * Class m200212_070225_add_table_work_group_sending
 */
class m200212_070225_add_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_group_sending}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID'),
			'agentid'     => $this->integer(11)->unsigned()->comment('授权方应用id'),
			'title'       => $this->char(32)->comment('消息名称'),
			'send_type'   => $this->tinyInteger(1)->comment('1全部2按条件筛选'),
			'push_type'   => $this->tinyInteger(1)->defaultValue(0)->comment('0立即发送1指定时间发送'),
			'push_time'   => $this->timestamp()->comment('发送时间'),
			'queue_id'    => $this->tinyInteger(1)->defaultValue(0)->comment('队列id'),
			'user_key'    => $this->text()->comment('选择的成员或客户标志'),
			'content'     => $this->text()->comment('发送内容'),
			'status'      => $this->tinyInteger(1)->defaultValue(0)->comment('发送状态 0未发送 1已发送 2发送失败'),
			'is_del'      => $this->tinyInteger(1)->defaultValue(0)->comment('删除状态 0 未删除 1 已删除'),
			'create_time' => $this->timestamp()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信群发表\'');

		$this->addForeignKey('KEY_WORK_GROUP_SENDING_CORPID', '{{%work_group_sending}}', 'corp_id', '{{%work_corp}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200212_070225_add_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200212_070225_add_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
