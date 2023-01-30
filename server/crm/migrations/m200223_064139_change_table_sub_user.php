<?php

use yii\db\Migration;

/**
 * Class m200223_064139_change_table_sub_user
 */
class m200223_064139_change_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%sub_user}}', 'work_uid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200223_064139_change_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200223_064139_change_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
