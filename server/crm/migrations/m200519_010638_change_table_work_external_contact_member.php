<?php

use yii\db\Migration;

/**
 * Class m200519_010638_change_table_work_external_contact_member
 */
class m200519_010638_change_table_work_external_contact_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_external_contact_member}}", "is_bind", "tinyint(1) DEFAULT '1' COMMENT '1已绑定 0未绑定' after `uc_id`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200519_010638_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200519_010638_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }
    */
}
