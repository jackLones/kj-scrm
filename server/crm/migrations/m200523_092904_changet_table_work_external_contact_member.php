<?php

use yii\db\Migration;

/**
 * Class m200523_092904_changet_table_work_external_contact_member
 */
class m200523_092904_changet_table_work_external_contact_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_external_contact_member}}', 'member_id', 'char(32) COMMENT \'会员id或有赞手机号\'');
	    $this->alterColumn('{{%work_external_contact_member}}', 'uc_id', 'char(32) COMMENT \'用户id或有赞手机号\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200523_092904_changet_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200523_092904_changet_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }
    */
}
