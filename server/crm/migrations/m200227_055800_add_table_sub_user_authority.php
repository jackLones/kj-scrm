<?php

use yii\db\Migration;

/**
 * Class m200227_055800_add_table_sub_user_authority
 */
class m200227_055800_add_table_sub_user_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%sub_user_authority}}', [
			'id'            => $this->primaryKey(11)->unsigned(),
			'sub_user_id'   => $this->integer(11)->unsigned()->comment('员工id'),
			'authority_ids' => $this->text()->comment('权限id'),
			'create_time'   => $this->timestamp()->comment('创建时间'),
			'update_time'   => $this->timestamp()->comment('更新时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'员工权限表\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_055800_add_table_sub_user_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_055800_add_table_sub_user_authority cannot be reverted.\n";

        return false;
    }
    */
}
