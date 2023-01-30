<?php

use yii\db\Migration;

/**
 * Class m200319_023903_change_table_awards_activity
 */
class m200319_023903_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%awards_activity}}', 'content');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200319_023903_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200319_023903_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
