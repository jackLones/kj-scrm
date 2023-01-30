<?php

use yii\db\Migration;

/**
 * Class m200117_065343_change_table_attachment_group
 */
class m200117_065343_change_table_attachment_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment_group}}', 'parent_id', 'int(11) unsigned COMMENT \'父级ID\' AFTER `uid`');
	    $this->addForeignKey('KEY_ATTACHMENT_PARENTID', '{{%attachment_group}}', 'parent_id', '{{%attachment_group}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_065343_change_table_attachment_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_065343_change_table_attachment_group cannot be reverted.\n";

        return false;
    }
    */
}
