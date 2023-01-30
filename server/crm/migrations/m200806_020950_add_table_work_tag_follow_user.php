<?php

use yii\db\Migration;

/**
 * Class m200806_020950_add_table_work_tag_follow_user
 */
class m200806_020950_add_table_work_tag_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_tag_follow_user}}', [
			'id'             => $this->primaryKey(11)->unsigned(),
			'corp_id'        => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('授权的企业ID'),
			'tag_id'         => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('授权的企业的标签ID'),
			'follow_user_id' => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('外部联系人对应的ID'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客户标签表\'');

		$this->addForeignKey('KEY_WORK_TAG_FOLLOW_USER_CORP_ID', '{{%work_tag_follow_user}}', 'corp_id', '{{%work_corp}}', 'id');
		$this->addForeignKey('KEY_WORK_TAG_FOLLOW_USER_TAG_ID', '{{%work_tag_follow_user}}', 'tag_id', '{{%work_tag}}', 'id');
		$this->addForeignKey('KEY_WORK_TAG_FOLLOW_USER_FOLLOW_USER_ID', '{{%work_tag_follow_user}}', 'follow_user_id', '{{%work_external_contact_follow_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200806_020950_add_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200806_020950_add_table_work_tag_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
