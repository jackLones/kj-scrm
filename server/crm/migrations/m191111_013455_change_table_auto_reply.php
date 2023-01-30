<?php

use yii\db\Migration;

/**
 * Class m191111_013455_change_table_auto_reply
 */
class m191111_013455_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'tags_select_type', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'0 不限 1 特定标签 \'');
	    $this->addColumn('{{%auto_reply}}', 'tags_id', 'VARCHAR(255)  NOT NULL DEFAULT "" COMMENT \'标签id，逗号隔开 \'');
	    $this->addColumn('{{%auto_reply}}', 'time_rule', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'0立即推送 1指定时间推送\'');
	    $this->addColumn('{{%auto_reply}}', 'push_time', 'timestamp NULL DEFAULT NULL COMMENT \'发送时间\'');
	    $this->addColumn('{{%auto_reply}}', 'queue_id', 'INT(11) unsigned DEFAULT "0" COMMENT \'队列id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_013455_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_013455_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
