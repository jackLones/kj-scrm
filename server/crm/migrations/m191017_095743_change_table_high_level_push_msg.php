<?php

use yii\db\Migration;

/**
 * Class m191017_095743_change_table_high_level_push_msg
 */
class m191017_095743_change_table_high_level_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%high_level_push_msg}}', 'target_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'预计发送粉丝数 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'fans_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'发送成功粉丝数 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'queue_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'队列id \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'msg_id', 'text COMMENT \'消息发送任务的ID，多个已逗号隔开 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'error_code', 'int(11) unsigned DEFAULT \'0\' COMMENT \'错误码 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'error_msg', 'varchar(64) NOT NULL DEFAULT \'\' COMMENT \'错误信息 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0未发送 1已发送 2发送失败 \' ');
	    $this->addColumn('{{%high_level_push_msg}}', 'is_del', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0未删除 1已删除 \' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191017_095743_change_table_high_level_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191017_095743_change_table_high_level_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
