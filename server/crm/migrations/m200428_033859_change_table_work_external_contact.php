<?php

use yii\db\Migration;

/**
 * Class m200428_033859_change_table_work_external_contact
 */
class m200428_033859_change_table_work_external_contact extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact}}", "name_convert", "varchar(255) NOT NULL DEFAULT '' COMMENT '客户姓名（解码后）' after `name`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200428_033859_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_033859_change_table_work_external_contact cannot be reverted.\n";

        return false;
    }
    */
}
