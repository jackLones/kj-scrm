<?php

use yii\db\Migration;

/**
 * Class m200209_034959_change_table_work_contact_way
 */
class m200209_034959_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way}}', 'title', 'varchar(200) DEFAULT NULL COMMENT \'活码名称\' AFTER `config_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200209_034959_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200209_034959_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
