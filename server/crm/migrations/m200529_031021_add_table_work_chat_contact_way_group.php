<?php

use yii\db\Migration;

/**
 * Class m200529_031021_add_table_work_chat_contact_way_group
 */
class m200529_031021_add_table_work_chat_contact_way_group extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_chat_contact_way_group}}', [
			'id'           => $this->primaryKey(11)->unsigned(),
			'uid'          => $this->integer(11)->unsigned()->comment('用户ID'),
			'corp_id'      => $this->integer(11)->unsigned()->comment('企业ID'),
			'parent_id'    => $this->integer(11)->unsigned()->comment('分组父级ID'),
			'title'        => $this->string(32)->comment('分组名称'),
			'status'       => $this->tinyInteger(1)->defaultValue(1)->comment('1可用 0不可用'),
			'update_time'  => $this->timestamp()->comment('修改时间'),
			'create_time'  => $this->timestamp()->comment('创建时间'),
			'sort'         => $this->integer(11)->defaultValue(0)->comment('分组排序'),
			'is_not_group' => $this->tinyInteger(1)->defaultValue(0)->comment('0已分组、1未分组')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群聊活码分组表\'');
		$this->addForeignKey('KEY_WORK_CHAT_CONTACT_WAY_GROUP_UID', '{{%work_chat_contact_way_group}}', 'uid', '{{%user}}', 'uid');
		$this->addForeignKey('KEY_WORK_CHAT_CONTACT_WAY_GROUP_CORPID', '{{%work_chat_contact_way_group}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WORK_CHAT_CONTACT_WAY_GROUP_PARENTID', '{{%work_chat_contact_way_group}}', 'parent_id', '{{%work_contact_way_group}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_031021_add_table_work_chat_contact_way_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_031021_add_table_work_chat_contact_way_group cannot be reverted.\n";

        return false;
    }
    */
}
