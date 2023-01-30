<?php

use yii\db\Migration;

/**
 * Class m200617_031640_change_table_work_external_contact_follow_user
 */
class m200617_031640_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'update_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'最后一次跟进状态时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200617_031640_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200617_031640_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
