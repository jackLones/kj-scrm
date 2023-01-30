<?php

use yii\db\Migration;

/**
 * Class m191011_012349_change_table_template_push_msg
 */
class m191011_012349_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}', 'fans_num', 'int(11) unsigned DEFAULT 0 COMMENT \'发送成功粉丝数\' ');
	    $this->addColumn('{{%template_push_msg}}', 'status', 'tinyint(1) DEFAULT 0 COMMENT \'状态 0未发送 1已发送 2发送失败\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191011_012349_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191011_012349_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
