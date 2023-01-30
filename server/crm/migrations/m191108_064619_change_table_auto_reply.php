<?php

use yii\db\Migration;

/**
 * Class m191108_064619_change_table_auto_reply
 */
class m191108_064619_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'push_type', 'tinyint(1) unsigned DEFAULT \'1\' COMMENT \'推送方式，1：随机推送一条、2：全部推送\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191108_064619_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191108_064619_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
