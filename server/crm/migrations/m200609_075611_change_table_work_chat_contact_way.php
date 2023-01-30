<?php

use yii\db\Migration;

/**
 * Class m200609_075611_change_table_work_chat_contact_way
 */
class m200609_075611_change_table_work_chat_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%work_chat_contact_way}}', 'create_time', 'timestamp NULL COMMENT \'创建时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200609_075611_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200609_075611_change_table_work_chat_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
