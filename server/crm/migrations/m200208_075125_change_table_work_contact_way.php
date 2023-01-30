<?php

use yii\db\Migration;

/**
 * Class m200208_075125_change_table_work_contact_way
 */
class m200208_075125_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way}}', 'user_key', 'varchar(255) DEFAULT NULL COMMENT \'用户选择的key值\' AFTER `tag_ids`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200208_075125_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200208_075125_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
