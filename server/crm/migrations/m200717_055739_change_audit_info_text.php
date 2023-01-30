<?php

use yii\db\Migration;

/**
 * Class m200717_055739_change_audit_info_text
 */
class m200717_055739_change_audit_info_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_msg_audit_info_text}}', 'content_convert', 'text NOT NULL COMMENT \'消息内容（解码后）\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200717_055739_change_audit_info_text cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200717_055739_change_audit_info_text cannot be reverted.\n";

        return false;
    }
    */
}
