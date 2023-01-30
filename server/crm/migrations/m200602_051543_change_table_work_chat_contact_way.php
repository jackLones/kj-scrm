<?php

use yii\db\Migration;

/**
 * Class m200602_051543_change_table_work_chat_contact_way
 */
class m200602_051543_change_table_work_chat_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropColumn('{{%work_chat_contact_way}}', 'type');
		$this->dropColumn('{{%work_chat_contact_way}}', 'scene');
		$this->dropColumn('{{%work_chat_contact_way}}', 'style');
		$this->dropColumn('{{%work_chat_contact_way}}', 'remark');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_051543_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_051543_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
