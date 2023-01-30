<?php

use yii\db\Migration;

/**
 * Class m200805_054839_add_table_work_per_tag_follow_user
 */
class m200805_054839_add_table_work_per_tag_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_per_tag_follow_user}}', [
			'id'             => $this->primaryKey(11)->unsigned(),
			'corp_id'        => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('企业微信ID'),
			'group_name'     => $this->char(255)->defaultValue(NULL)->comment('标签分组名称'),
			'tag_name'       => $this->char(255)->defaultValue(NULL)->comment('标签名称'),
			'status'         => $this->tinyInteger(1)->defaultValue(0)->comment('状态0不显示1显示'),
			'follow_user_id' => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('外部联系人对应的ID'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客户个人标签表\'');

		$this->addForeignKey('KEY_WORK_PER_TAG_FOLLOW_USER_CORP_ID', '{{%work_per_tag_follow_user}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WORK_PER_TAG_FOLLOW_USER_FOLLOW_USER_ID', '{{%work_per_tag_follow_user}}', 'follow_user_id', '{{%work_external_contact_follow_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200805_054839_add_table_work_per_tag_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200805_054839_add_table_work_per_tag_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
