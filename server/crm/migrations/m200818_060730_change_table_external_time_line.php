<?php

use yii\db\Migration;

/**
 * Class m200818_060730_change_table_external_time_line
 */
class m200818_060730_change_table_external_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%external_time_line}}', 'openid', 'varchar(64) NOT NULL DEFAULT \'\' COMMENT \'用户openid\' AFTER `related_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200818_060730_change_table_external_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_060730_change_table_external_time_line cannot be reverted.\n";

        return false;
    }
    */
}
