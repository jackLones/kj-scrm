<?php

use yii\db\Migration;

/**
 * Class m200811_022610_change_table_package
 */
class m200811_022610_change_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package}}', 'sort', 'int(11) DEFAULT 0 COMMENT \'套餐等级排序\' AFTER `is_agent`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_022610_change_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_022610_change_table_package cannot be reverted.\n";

        return false;
    }
    */
}
