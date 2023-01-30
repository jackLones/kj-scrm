<?php

use yii\db\Migration;

/**
 * Class m191106_012647_change_table_template_push_msg
 */
class m191106_012647_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}', 'error_code', 'int(11) unsigned DEFAULT \'0\' COMMENT \'错误码 \' ');
	    $this->addColumn('{{%template_push_msg}}', 'error_msg', 'varchar(64) NOT NULL DEFAULT \'\' COMMENT \'错误信息 \' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191106_012647_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191106_012647_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
