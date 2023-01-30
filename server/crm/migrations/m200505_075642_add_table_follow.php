<?php

use yii\db\Migration;

/**
 * Class m200505_075642_add_table_follow
 */
class m200505_075642_add_table_follow extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%follow}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'uid'         => $this->integer(11)->unsigned()->comment('用户ID'),
			'title'       => $this->string(16)->defaultValue('')->comment('名称'),
			'status'      => $this->tinyInteger(1)->defaultValue(1)->comment('1可用 0删除'),
			'update_time' => $this->timestamp()->comment('修改时间'),
			'create_time' => $this->timestamp()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'跟进状态表\'');
		$this->addForeignKey('KEY_FOLLOW_UID', '{{%follow}}', 'uid', '{{%user}}', 'uid');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200505_075642_add_table_follow cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200505_075642_add_table_follow cannot be reverted.\n";

        return false;
    }
    */
}
