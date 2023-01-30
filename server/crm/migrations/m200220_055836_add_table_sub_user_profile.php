<?php

use yii\db\Migration;

/**
 * Class m200220_055836_add_table_sub_user_profile
 */
class m200220_055836_add_table_sub_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%sub_user_profile}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'sub_user_id' => $this->integer(11)->unsigned()->comment('子账户id'),
			'name'        => $this->string(30)->comment('姓名'),
			'sex'         => $this->tinyInteger(1)->comment('性别'),
			'department'  => $this->string(50)->comment('部门'),
			'position'    => $this->string(50)->comment('职务'),
			'update_time' => $this->timestamp()->comment('修改时间'),
			'create_time' => $this->timestamp()->comment('创建时间')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'子账户详情表\'');
		$this->addForeignKey('KEY_SUB_USER_ID', '{{%sub_user_profile}}', 'sub_user_id', '{{%sub_user}}', 'sub_id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_055836_add_table_sub_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_055836_add_table_sub_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
