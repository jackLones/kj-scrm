<?php

use yii\db\Migration;

/**
 * Class m200706_064026_change_table_work_external_contact_member
 */
class m200706_064026_change_table_work_external_contact_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_member}}', 'follow_user_id', 'int(11) unsigned DEFAULT NULL COMMENT \'企业微信外部联系人ID\'');
	    $this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_MEMBER_USER_ID', '{{%work_external_contact_member}}', 'follow_user_id', '{{%work_external_contact_follow_user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200706_064026_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200706_064026_change_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }
    */
}
