<?php

use yii\db\Migration;

/**
 * Class m200327_122241_add_table_awards_records
 */
class m200327_122241_add_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_records}}', 'join_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'参与人id\' AFTER `award_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_122241_add_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_122241_add_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
