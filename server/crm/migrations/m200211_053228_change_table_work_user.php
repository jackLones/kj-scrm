<?php

use yii\db\Migration;

/**
 * Class m200211_053228_change_table_work_user
 */
class m200211_053228_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_user}}', 'new_apply_cnt', 'int(11) unsigned DEFAULT \'0\' COMMENT \'发起申请数\'');
		$this->addColumn('{{%work_user}}', 'new_contact_cnt', ' int(11) unsigned DEFAULT \'0\' COMMENT \'新增客户数\'');
		$this->addColumn('{{%work_user}}', 'negative_feedback_cnt', ' int(11) unsigned DEFAULT \'0\' COMMENT \'删除/拉黑成员的客户数\'');
		$this->addColumn('{{%work_user}}', 'chat_cnt', ' int(11) unsigned DEFAULT \'0\' COMMENT \'聊天总数\'');
		$this->addColumn('{{%work_user}}', 'message_cnt', 'int(11) unsigned DEFAULT \'0\' COMMENT \'发送消息数\'');
		$this->addColumn('{{%work_user}}', 'reply_percentage', ' char(8) DEFAULT \'\' COMMENT \'已回复聊天占比\'');
		$this->addColumn('{{%work_user}}', 'avg_reply_time', ' char(16) DEFAULT \'\' COMMENT \'平均首次回复时长(分钟)\'');
		$this->createIndex('KEY_NEW_APPLY_CNT', '{{%work_user}}', 'new_apply_cnt');
		$this->createIndex('KEY_NEW_CONTACT_CNT', '{{%work_user}}', 'new_contact_cnt');
		$this->createIndex('KEY_CHAT_CNT', '{{%work_user}}', 'chat_cnt');
		$this->createIndex('KEY_MESSAGE_CNT', '{{%work_user}}', 'message_cnt');

	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200211_053228_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200211_053228_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
