<?php

use yii\db\Migration;

/**
 * Class m191111_083153_change_table_scene
 */
class m191111_083153_change_table_scene extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%scene}}', 'reply_sort', 'char(64) DEFAULT \'\' COMMENT \'消息回复的排序，多个时用逗号分割\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_083153_change_table_scene cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_083153_change_table_scene cannot be reverted.\n";

        return false;
    }
    */
}
