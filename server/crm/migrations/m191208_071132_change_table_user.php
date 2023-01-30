<?php

use yii\db\Migration;

/**
 * Class m191208_071132_change_table_user
 */
class m191208_071132_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'message_num', 'int(11) unsigned DEFAULT \'0\' COMMENT \'短信数量\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191208_071132_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191208_071132_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
