<?php

use yii\db\Migration;

/**
 * Class m190917_034843_add_column_time_into_table_fans_behavior
 */
class m190917_034843_change_table_fans_behavior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('{{%fans_behavior}}', 'hour', 'int(2) UNSIGNED NULL COMMENT \'时\' AFTER `day`');
    	$this->addColumn('{{%fans_behavior}}', 'minute', 'int(2) UNSIGNED NULL COMMENT \'分\' AFTER `hour`');
    	$this->addColumn('{{%fans_behavior}}', 'second', 'int(2) UNSIGNED NULL COMMENT \'秒\' AFTER `minute`');
    	$this->addColumn('{{%fans_behavior}}', 'time', 'int(11) UNSIGNED NULL COMMENT \'发生时间\' AFTER `second`');

    	$this->createIndex('KEY_FANS_BEHAVIOR_YEAR', '{{%fans_behavior}}', 'year');
    	$this->createIndex('KEY_FANS_BEHAVIOR_MONTH', '{{%fans_behavior}}', 'month');
    	$this->createIndex('KEY_FANS_BEHAVIOR_DAY', '{{%fans_behavior}}', 'day');
    	$this->createIndex('KEY_FANS_BEHAVIOR_HOUR', '{{%fans_behavior}}', 'hour');
    	$this->createIndex('KEY_FANS_BEHAVIOR_MINUTE', '{{%fans_behavior}}', 'minute');
    	$this->createIndex('KEY_FANS_BEHAVIOR_SECOND', '{{%fans_behavior}}', 'second');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_034843_add_column_time_into_table_fans_behavior cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_034843_add_column_time_into_table_fans_behavior cannot be reverted.\n";

        return false;
    }
    */
}
