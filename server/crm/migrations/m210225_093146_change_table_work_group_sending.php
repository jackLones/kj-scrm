<?php

use yii\db\Migration;

/**
 * Class m210225_093146_change_table_work_group_sending
 */
class m210225_093146_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_group_sending}}', 'user_key', 'longtext COMMENT \'选择的成员或客户标志\'');
	    $this->alterColumn('{{%work_group_sending}}', 'others', 'longtext COMMENT \'客户其他筛选字段值\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210225_093146_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210225_093146_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
