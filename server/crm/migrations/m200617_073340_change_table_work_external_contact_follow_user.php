<?php

use yii\db\Migration;

/**
 * Class m200617_073340_change_table_work_external_contact_follow_user
 */
class m200617_073340_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_external_contact_follow_user}}', 'nickname', 'char(64) DEFAULT \'\' COMMENT \'设置的用户昵称备注\'');
		$this->addColumn('{{%work_external_contact_follow_user}}', 'des', 'varchar(255) DEFAULT \'\' COMMENT \'设置的用户描述\'');
		$this->addColumn('{{%work_external_contact_follow_user}}', 'close_rate', 'int(11) unsigned DEFAULT NULL COMMENT \'预计成交率\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_073340_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_073340_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
