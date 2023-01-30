<?php

use yii\db\Migration;

/**
 * Class m190917_063627_add_index_into_table_fans
 */
class m190917_063627_add_index_into_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->createIndex('KEY_FANS_COUNTRY', '{{%fans}}', 'country');
    	$this->createIndex('KEY_FANS_PROVINCE', '{{%fans}}', 'province');
    	$this->createIndex('KEY_FANS_CITY', '{{%fans}}', 'city');
    	$this->createIndex('KEY_FANS_SUBSCRIBESCENE', '{{%fans}}', 'subscribe_scene');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_063627_add_index_into_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_063627_add_index_into_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
