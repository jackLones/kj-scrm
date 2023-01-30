<?php

use yii\db\Migration;

/**
 * Class m200814_020403_change_table_work_import_customer
 */
class m200814_020403_change_table_work_import_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_import_customer}}', 'all_num', 'int(11) NOT NULL DEFAULT 0 COMMENT \'总数据条数\' after `user_ids`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200814_020403_change_table_work_import_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200814_020403_change_table_work_import_customer cannot be reverted.\n";

        return false;
    }
    */
}
