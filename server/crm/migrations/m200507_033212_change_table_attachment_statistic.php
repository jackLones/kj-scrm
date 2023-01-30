<?php

use yii\db\Migration;

/**
 * Class m200507_033212_change_table_attachment_statistic
 */
class m200507_033212_change_table_attachment_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%attachment_statistic}}", "corp_id", "int(11) unsigned DEFAULT NULL COMMENT '状态id' AFTER `attachment_id` ");
	    $this->addForeignKey('KEY_ATTACHMENT_STATISTIC_CORPID', '{{%attachment_statistic}}', 'corp_id', '{{%work_corp}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200507_033212_change_table_attachment_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200507_033212_change_table_attachment_statistic cannot be reverted.\n";

        return false;
    }
    */
}
