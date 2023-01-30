<?php

use yii\db\Migration;

/**
 * Class m200915_055348_add_table_wait_status
 */
class m200915_055348_add_table_wait_status extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_status}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'uid'         => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('用户ID'),
			'title'       => $this->char(64)->defaultValue(NULL)->comment('待办项目阶段'),
			'color'       => $this->char(32)->defaultValue(NULL)->comment('颜色'),
			'desc'        => $this->char(255)->defaultValue(NULL)->comment('描述'),
			'sort'        => $this->integer(10)->defaultValue(NULL)->comment('排序 越小越靠前'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目执行状态表\'');

		$this->addForeignKey('KEY_WAIT_STATUS_UID', '{{%wait_status}}', 'uid', '{{%user}}', 'uid');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_055348_add_table_wait_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_055348_add_table_wait_status cannot be reverted.\n";

        return false;
    }
    */
}
