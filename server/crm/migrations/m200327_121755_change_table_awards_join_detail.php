<?php

use yii\db\Migration;

/**
 * Class m200327_121755_change_table_awards_join_detail
 */
class m200327_121755_change_table_awards_join_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addForeignKey('KEY_AWARDS_JOIN_ID', '{{%awards_join_detail}}', 'awards_join_id', '{{%awards_join}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_121755_change_table_awards_join_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_121755_change_table_awards_join_detail cannot be reverted.\n";

        return false;
    }
    */
}
