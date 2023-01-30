<?php

use yii\db\Migration;

/**
 * Class m210410_062701_add_column_into_table_work_import_customer_detail
 */
class m210410_062701_add_column_into_table_work_import_customer_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_import_customer_detail}}', 'distribution_records', 'text DEFAULT NULL COMMENT \'分配员工记录\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_import_customer_detail}}', 'distribution_records');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210410_062701_add_column_into_table_work_import_customer_detail cannot be reverted.\n";

        return false;
    }
    */
}
