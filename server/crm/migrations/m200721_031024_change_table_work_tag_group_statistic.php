<?php

use yii\db\Migration;

/**
 * Class m200721_031024_change_table_work_tag_group_statistic
 */
class m200721_031024_change_table_work_tag_group_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_group_statistic}}', 'push_time', 'timestamp NULL DEFAULT NULL  COMMENT \'发送时间\' AFTER `send`');
		$this->addColumn('{{%work_tag_group_statistic}}', 'user_id', 'int(11) unsigned DEFAULT NULL  COMMENT \'成员ID\' AFTER `external_id`');
		$this->addForeignKey('KEY_WORK_TAG_GROUP_STATISTIC_USER_ID', '{{%work_tag_group_statistic}}', 'user_id', '{{%work_user}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200721_031024_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200721_031024_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }
    */
}
