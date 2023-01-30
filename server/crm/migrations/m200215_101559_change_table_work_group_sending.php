<?php

use yii\db\Migration;

/**
 * Class m200215_101559_change_table_work_group_sending
 */
class m200215_101559_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'fail_list', 'text COMMENT \'失败的人员\' AFTER `error_code`');
	    $this->addColumn('{{%work_group_sending}}', 'msgid', 'varchar(200) COMMENT \'群发消息id\' AFTER `error_code`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200215_101559_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200215_101559_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
