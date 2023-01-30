<?php

use yii\db\Migration;

/**
 * Class m201125_114103_change_table_work_user
 */
class m201125_114103_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_user}}', 'can_send_money', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'是否可发红包1是0否\' after `day_user_money`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201125_114103_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201125_114103_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
