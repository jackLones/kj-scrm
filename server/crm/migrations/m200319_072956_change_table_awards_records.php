<?php

use yii\db\Migration;

/**
 * Class m200319_072956_change_table_awards_records
 */
class m200319_072956_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_records}}', 'user_id', 'char(100) DEFAULT NULL COMMENT \'参与者身份\' AFTER `award_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200319_072956_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200319_072956_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
