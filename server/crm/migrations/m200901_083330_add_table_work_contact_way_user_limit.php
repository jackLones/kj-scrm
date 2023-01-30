<?php

use yii\db\Migration;

/**
 * Class m200901_083330_add_table_work_contact_way_user_limit
 */
class m200901_083330_add_table_work_contact_way_user_limit extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_user_limit}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'way_id'      => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('渠道活码ID'),
			'user_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('成员ID'),
			'name'     => $this->char(64)->defaultValue(NULL)->comment('员工名称'),
			'limit'       => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('每天添加的上限'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码成员添加客户上限表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_USER_LIMIT_WAY_ID', '{{%work_contact_way_user_limit}}', 'way_id', '{{%work_contact_way}}', 'id');
		$this->addForeignKey('KEY_WORK_CONTACT_WAY_USER_LIMIT_USER_ID', '{{%work_contact_way_user_limit}}', 'user_id', '{{%work_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_083330_add_table_work_contact_way_user_limit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_083330_add_table_work_contact_way_user_limit cannot be reverted.\n";

        return false;
    }
    */
}
