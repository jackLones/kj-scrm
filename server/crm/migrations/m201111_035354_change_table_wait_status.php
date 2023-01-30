<?php

use yii\db\Migration;

/**
 * Class m201111_035354_change_table_wait_status
 */
class m201111_035354_change_table_wait_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wait_status}}', 'is_del', 'tinyint(1) DEFAULT 0 COMMENT \'是否删除1已删除0未删除\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201111_035354_change_table_wait_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201111_035354_change_table_wait_status cannot be reverted.\n";

        return false;
    }
    */
}
