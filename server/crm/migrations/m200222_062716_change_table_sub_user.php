<?php

use yii\db\Migration;

/**
 * Class m200222_062716_change_table_sub_user
 */
class m200222_062716_change_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('KEY_SUB_USER_WORK_UID','{{%sub_user}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_062716_change_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_062716_change_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
