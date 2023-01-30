<?php

use yii\db\Migration;

/**
 * Class m201209_022046_change_table_work_msg_audit_user
 */
class m201209_022046_change_table_work_msg_audit_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_msg_audit_user}}', 'keyword_status', 'tinyint(1) DEFAULT 0 COMMENT \'智能推荐状态：0未设置、1开启、2关闭\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201209_022046_change_table_work_msg_audit_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201209_022046_change_table_work_msg_audit_user cannot be reverted.\n";

        return false;
    }
    */
}
