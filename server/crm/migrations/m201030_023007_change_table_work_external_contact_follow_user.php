<?php

use yii\db\Migration;

/**
 * Class m201030_023007_change_table_work_external_contact_follow_user
 */
class m201030_023007_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact_follow_user}}', 'punch_id', 'int(11) unsigned DEFAULT NULL COMMENT \'群打卡ID\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201030_023007_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201030_023007_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
