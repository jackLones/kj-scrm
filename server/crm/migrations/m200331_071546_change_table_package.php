<?php

use yii\db\Migration;

/**
 * Class m200331_071546_change_table_package
 */
class m200331_071546_change_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package}}', 'message_num', 'int(11) UNSIGNED COMMENT \'消息配额\' AFTER `price`');
	    $this->addColumn('{{%package}}', 'sub_account_num', 'int(11) UNSIGNED COMMENT \'子账户数量\' AFTER `message_num`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200331_071546_change_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200331_071546_change_table_package cannot be reverted.\n";

        return false;
    }
    */
}
