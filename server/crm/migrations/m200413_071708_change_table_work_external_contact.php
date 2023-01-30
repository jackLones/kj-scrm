<?php

use yii\db\Migration;

/**
 * Class m200413_071708_change_table_work_external_contact
 */
class m200413_071708_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact}}", "nickname", "char(64) DEFAULT '' COMMENT '设置的用户昵称'");
	    $this->addColumn("{{%work_external_contact}}", "des", "varchar(255) DEFAULT '' COMMENT '设置的用户描述'");
	    $this->addColumn("{{%work_external_contact}}", "close_rate", "int(3) DEFAULT NULL COMMENT '预计成交率'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200413_071708_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200413_071708_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
