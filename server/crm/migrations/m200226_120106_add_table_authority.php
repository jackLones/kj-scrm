<?php

use yii\db\Migration;

/**
 * Class m200226_120106_add_table_authority
 */
class m200226_120106_add_table_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%authority}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'level'       => $this->tinyInteger(2)->unsigned()->comment('权限等级'),
			'name'        => $this->string(50)->comment('权限名称'),
			'route'       => $this->string(50)->comment('权限相关路由'),
			'description' => $this->char(255)->comment('权限简介'),
			'status'      => $this->tinyInteger(2)->defaultValue(0)->comment('状态0未删除1已删除'),
			'create_time' => $this->timestamp()->comment('创建时间'),
			'update_time' => $this->timestamp()->comment('更新时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'权限表\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200226_120106_add_table_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_120106_add_table_authority cannot be reverted.\n";

        return false;
    }
    */
}
