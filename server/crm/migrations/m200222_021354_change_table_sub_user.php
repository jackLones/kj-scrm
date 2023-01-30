<?php

use yii\db\Migration;

/**
 * Class m200222_021354_change_table_sub_user
 */
class m200222_021354_change_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%sub_user}}', 'work_uid', ' int(11) unsigned DEFAULT \'0\' COMMENT \'企业微信的员工id\' AFTER `uid` ');
	    $this->addForeignKey('KEY_SUB_USER_WORK_UID', '{{%sub_user}}', 'work_uid', '{{%work_user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_021354_change_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_021354_change_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
