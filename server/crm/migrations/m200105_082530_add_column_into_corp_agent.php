<?php

use yii\db\Migration;

/**
 * Class m200105_082530_add_column_into_corp_agent
 */
class m200105_082530_add_column_into_corp_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('{{%work_corp_agent}}', 'is_del', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：未删除；1：已删除\' AFTER `extra_tag`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200105_082530_add_column_into_corp_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200105_082530_add_column_into_corp_agent cannot be reverted.\n";

        return false;
    }
    */
}
