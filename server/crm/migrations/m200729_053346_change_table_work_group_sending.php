<?php

use yii\db\Migration;

/**
 * Class m200729_053346_change_table_work_group_sending
 */
class m200729_053346_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%work_group_sending}}', 'times');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200729_053346_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200729_053346_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
