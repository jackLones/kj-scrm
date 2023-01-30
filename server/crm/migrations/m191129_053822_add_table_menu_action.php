<?php

use yii\db\Migration;

/**
 * Class m191129_053822_add_table_menu_action
 */
class m191129_053822_add_table_menu_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%menu_action}}', [
		    'id'           => $this->primaryKey(11)->unsigned(),
		    'model'        => $this->string(32)->comment('模块名'),
		    'control'      => $this->string(32)->comment('控制器名'),
		    'action'       => $this->string(32)->comment('方法名'),
		    'method'       => $this->tinyInteger(1)->comment('1：get、2：post、3：ajax(get)、4：ajax(post)、5：内部调用、6：其他'),
		    'status'       => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('是否启用，0：不启用、1：启用'),
		    'introduction' => $this->string(250)->comment('介绍'),
		    'update_time'  => $this->timestamp()->comment('修改时间'),
		    'create_time'  => $this->timestamp()->comment('创建时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'菜单方法表\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191129_053822_add_table_menu_action cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191129_053822_add_table_menu_action cannot be reverted.\n";

        return false;
    }
    */
}
