<?php

use yii\db\Migration;

/**
 * Class m191127_061545_add_table_package_menu
 */
class m191127_061545_add_table_package_menu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%package_menu}}', [
		    'id'          => $this->primaryKey(11)->unsigned(),
		    'package_id'  => $this->integer(11)->unsigned()->comment('套餐id'),
		    'menu_id'     => $this->integer(11)->unsigned()->comment('菜单id'),
		    'status'      => $this->tinyInteger(1)->comment('状态，1：启用、0：不启用'),
		    'update_time' => $this->timestamp()->comment('修改时间'),
		    'create_time' => $this->timestamp()->comment('创建时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'套餐菜单关联表\'');
	    $this->addForeignKey('KEY_PACKAGE_MENU_PACKAGEID', '{{%package_menu}}', 'package_id', '{{%package}}', 'id');
	    $this->addForeignKey('KEY_PACKAGE_MENU_MENUID', '{{%package_menu}}', 'menu_id', '{{%menu}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191127_061545_add_table_package_menu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191127_061545_add_table_package_menu cannot be reverted.\n";

        return false;
    }
    */
}
