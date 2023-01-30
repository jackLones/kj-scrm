<?php

use yii\db\Migration;

/**
 * Class m200819_064500_add_table_work_dismiss_user_detail
 */
class m200819_064500_add_table_work_dismiss_user_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_dismiss_user_detail}}', [
			'id'               => $this->primaryKey(11)->unsigned(),
			'corp_id'          => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业微信ID'),
			'user_id'          => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业成员ID'),
			'external_userid'  => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('外部联系人ID'),
			'chat_id'          => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('群ID'),
			'status'           => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否分配1已分配0未分配'),
			'allocate_user_id' => $this->integer(11)->unsigned()->defaultValue(0)->comment('已分配成员'),
			'allocate_time'    => $this->integer(11)->unsigned()->defaultValue(0)->comment('分配时间'),
			'create_time'      => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'离职成员明细表\'');

		$this->addForeignKey('KEY_WORK_DISMISS_USER_DETAIL_CORP_ID', '{{%work_dismiss_user_detail}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WORK_DISMISS_USER_DETAIL_USER_ID', '{{%work_dismiss_user_detail}}', 'user_id', '{{%work_user}}', 'id');
		$this->addForeignKey('KEY_WORK_DISMISS_USER_DETAIL_EXTERNAL_USERID', '{{%work_dismiss_user_detail}}', 'external_userid', '{{%work_external_contact}}', 'id');
		$this->addForeignKey('KEY_WORK_DISMISS_USER_DETAIL_CHAT_ID', '{{%work_dismiss_user_detail}}', 'chat_id', '{{%work_chat}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200819_064500_add_table_work_dismiss_user_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200819_064500_add_table_work_dismiss_user_detail cannot be reverted.\n";

        return false;
    }
    */
}
