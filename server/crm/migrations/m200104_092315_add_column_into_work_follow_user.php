<?php

use yii\db\Migration;

/**
 * Class m200104_092315_add_column_into_work_follow_user
 */
class m200104_092315_add_column_into_work_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('{{%work_external_contact_follow_user}}', 'del_type', 'tinyint(2) NULL DEFAULT 0 COMMENT \'0：未删除；1：成员删除外部联系人；2：外部联系人删除成员\' AFTER `state`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200104_092315_add_column_into_work_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200104_092315_add_column_into_work_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
