<?php

use yii\db\Migration;

/**
 * Class m201215_052319_change_table_user
 */
class m201215_052319_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'is_hide_phone', 'tinyint(1) DEFAULT \'0\' COMMENT \'手机号是否隐藏1是0否\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201215_052319_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201215_052319_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
