<?php

use yii\db\Migration;

/**
 * Class m210106_034143_change_table_work_chat
 */
class m210106_034143_change_table_work_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat}}', 'group_chat', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'群组类型：0：外部；1：内部\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210106_034143_change_table_work_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210106_034143_change_table_work_chat cannot be reverted.\n";

        return false;
    }
    */
}
