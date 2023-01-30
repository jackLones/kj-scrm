<?php

use yii\db\Migration;

/**
 * Class m200914_005700_change_table_work_contact_way
 */
class m200914_005700_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way}}', 'spare_employee', 'text  DEFAULT NULL COMMENT \'备用员工\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_005700_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_005700_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
