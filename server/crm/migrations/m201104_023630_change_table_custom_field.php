<?php

use yii\db\Migration;

/**
 * Class m201104_023630_change_table_custom_field
 */
class m201104_023630_change_table_custom_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%custom_field}}', 'chat_status', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'群属性状态0关闭，1开启(仅自定义属性)\' AFTER `status` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201104_023630_change_table_custom_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201104_023630_change_table_custom_field cannot be reverted.\n";

        return false;
    }
    */
}
