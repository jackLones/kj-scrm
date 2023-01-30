<?php

use yii\db\Migration;

/**
 * Class m200726_020435_change_table_work_tag_group_statistic
 */
class m200726_020435_change_table_work_tag_group_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_tag_group_statistic}}', 'send_id', 'int(11) unsigned DEFAULT NULL COMMENT \'企业微信群发ID \' AFTER `pull_id`');
		$this->addForeignKey('KEY_WORK_TAG_GROUP_USER_STATISTIC_SEND_ID', '{{%work_tag_group_statistic}}', 'send_id', '{{%work_group_sending}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200726_020435_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200726_020435_change_table_work_tag_group_statistic cannot be reverted.\n";

        return false;
    }
    */
}
