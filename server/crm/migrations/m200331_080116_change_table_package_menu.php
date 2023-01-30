<?php

use yii\db\Migration;

/**
 * Class m200331_080116_change_table_package_menu
 */
class m200331_080116_change_table_package_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package_menu}}', 'use_limit', 'varchar(255) DEFAULT \'\' COMMENT \'功能限制使用数量\' AFTER `menu_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200331_080116_change_table_package_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200331_080116_change_table_package_menu cannot be reverted.\n";

        return false;
    }
    */
}
