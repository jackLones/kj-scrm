<?php

use yii\db\Migration;

/**
 * Class m200415_030626_change_table_work_external_contact
 */
class m200415_030626_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact}}", "follow_status", "tinyint(1) DEFAULT 0 COMMENT '跟进状态：0未跟进1跟进中2已拒绝3已成交'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200415_030626_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200415_030626_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
