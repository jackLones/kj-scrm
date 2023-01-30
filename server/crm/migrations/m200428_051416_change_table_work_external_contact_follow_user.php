<?php

use yii\db\Migration;

/**
 * Class m200428_051416_change_table_work_external_contact_follow_user
 */
class m200428_051416_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn("{{%work_external_contact_follow_user}}", "delete_type", "tinyint(2) DEFAULT '0' COMMENT '0：未删除；1：成员删除外部联系人；2：外部联系人删除成员'");
		$this->addColumn("{{%work_external_contact_follow_user}}", "repeat_type", "tinyint(2) DEFAULT '0' COMMENT '0已删除1再次添加'");
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200428_051416_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200428_051416_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
