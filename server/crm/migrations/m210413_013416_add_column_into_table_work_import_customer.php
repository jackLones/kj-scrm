<?php

use yii\db\Migration;

/**
 * Class m210413_013416_add_column_into_table_work_import_customer
 */
class m210413_013416_add_column_into_table_work_import_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_import_customer}}', 'distribution_records', 'text DEFAULT NULL COMMENT \'客户导入分配记录\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_import_customer}}', 'distribution_records');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210413_013416_add_column_into_table_work_import_customer cannot be reverted.\n";

        return false;
    }
    */
}
