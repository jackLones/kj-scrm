<?php

use yii\db\Migration;

/**
 * Class m200325_053317_change_table_awards_list
 */
class m200325_053317_change_table_awards_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_list}}', 'key', 'int(11) unsigned NOT NULL COMMENT \'当前奖项索引\'');
	    $this->addColumn('{{%awards_list}}', 'title', 'char(21) NOT NULL COMMENT \'奖项\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200325_053317_change_table_awards_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200325_053317_change_table_awards_list cannot be reverted.\n";

        return false;
    }
    */
}
