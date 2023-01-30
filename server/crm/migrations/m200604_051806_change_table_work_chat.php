<?php

use yii\db\Migration;

/**
 * Class m200604_051806_change_table_work_chat
 */
class m200604_051806_change_table_work_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_chat}}', 'notice', 'text COMMENT \'群公告\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_051806_change_table_work_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_051806_change_table_work_chat cannot be reverted.\n";

        return false;
    }
    */
}
