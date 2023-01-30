<?php

use yii\db\Migration;

/**
 * Class m200707_072711_change_table_work_follow_msg
 */
class m200707_072711_change_table_work_follow_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_follow_msg}}', 'follow_user', 'varchar(2000) DEFAULT NULL COMMENT \'接收成员\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200707_072711_change_table_work_follow_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200707_072711_change_table_work_follow_msg cannot be reverted.\n";

        return false;
    }
    */
}
