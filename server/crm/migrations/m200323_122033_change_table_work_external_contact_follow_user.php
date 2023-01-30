<?php

use yii\db\Migration;

/**
 * Class m200323_122033_change_table_work_external_contact_follow_user
 */
class m200323_122033_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact_follow_user}}', 'award_id', 'int(11) unsigned DEFAULT 0 COMMENT \'抽奖任务id\' AFTER `fission_id`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200323_122033_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_122033_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
