<?php

use yii\db\Migration;

/**
 * Class m200103_054415_change_table_menu
 */
class m200103_054415_change_table_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%menu}}', 'comefrom', 'tinyint(1) DEFAULT \'0\' COMMENT \'菜单归属：0公众号、1企业微信\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200103_054415_change_table_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200103_054415_change_table_menu cannot be reverted.\n";

        return false;
    }
    */
}
