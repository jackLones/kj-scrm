<?php

use yii\db\Migration;

/**
 * Class m200915_062819_add_table_wait_project
 */
class m200915_062819_add_table_wait_project extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%wait_project}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'corp_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业ID'),
			'title'       => $this->char(200)->defaultValue(NULL)->comment('项目名称'),
			'desc'        => $this->text()->defaultValue(NULL)->comment('项目描述'),
			'user_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('员工ID'),
			'finish_time' => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('项目需要在多少天完成'),
			'is_del'      => $this->tinyInteger(1)->defaultValue(0)->comment('0未删除1已删除'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'待办项目表\'');

		$this->addForeignKey('KEY_WAIT_PROJECT_CORP_ID', '{{%wait_project}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WAIT_PROJECT_USER_ID', '{{%wait_project}}', 'user_id', '{{%work_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200915_062819_add_table_wait_project cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200915_062819_add_table_wait_project cannot be reverted.\n";

        return false;
    }
    */
}
