<?php

use yii\db\Migration;

/**
 * Class m200326_065410_change_table_awards_activity
 */
class m200326_065410_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_activity}}', 'init_num', 'int(11) unsigned DEFAULT \'0\'  COMMENT \'初始次数\' AFTER `share_title`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200326_065410_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200326_065410_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
