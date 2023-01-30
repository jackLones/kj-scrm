<?php

use yii\db\Migration;

/**
 * Class m200915_061526_add_table_wait_level
 */
class m200915_061526_add_table_wait_level extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_level}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'uid'         => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('用户ID'),
			'title'       => $this->char(64)->defaultValue(NULL)->comment('优先级名称'),
			'color'       => $this->char(32)->defaultValue(NULL)->comment('颜色'),
			'desc'        => $this->char(255)->defaultValue(NULL)->comment('优先级描述'),
			'sort'        => $this->integer(10)->defaultValue(NULL)->comment('排序 越小越靠前'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目优先级表\'');

		$this->addForeignKey('KEY_WAIT_LEVEL_UID', '{{%wait_level}}', 'uid', '{{%user}}', 'uid');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_061526_add_table_wait_level cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_061526_add_table_wait_level cannot be reverted.\n";

        return false;
    }
    */
}
