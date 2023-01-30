<?php

use yii\db\Migration;

/**
 * Class m191225_021820_change_table_message_template
 */
class m191225_021820_change_table_message_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%message_template}}', 'uid', 'int(11) unsigned DEFAULT NULL COMMENT \'用户id\' AFTER `id` ');
	    $this->addColumn('{{%message_template}}', 'sign_id', 'int(11) unsigned DEFAULT NULL COMMENT \'签名id\' AFTER `uid` ');
	    $this->addColumn('{{%message_template}}', 'error_msg', 'varchar(250) DEFAULT \'\' COMMENT \'失败原因\'');
	    $this->addColumn('{{%message_template}}', 'apply_time', 'timestamp NULL DEFAULT NULL COMMENT \'申请时间\'');
	    $this->alterColumn('{{%message_template}}', 'status', 'tinyint(1) DEFAULT \'0\' COMMENT \'状态，-1：删除、0：待审核、1：已审核、2：审核失败\'');
	    $this->addForeignKey('KEY_MESSAGE_TEMPLATE_USERID', '{{%message_template}}', 'uid', '{{%user}}', 'uid');
	    $this->addForeignKey('KEY_MESSAGE_TEMPLATE_SIGNID', '{{%message_template}}', 'sign_id', '{{%message_sign}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191225_021820_change_table_message_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191225_021820_change_table_message_template cannot be reverted.\n";

        return false;
    }
    */
}
