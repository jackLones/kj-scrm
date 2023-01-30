<?php

use yii\db\Migration;

/**
 * Class m200327_121422_add_table_awards_join
 */
class m200327_121422_add_table_awards_join extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_AWARDS_JOIN_AWARD_ID', '{{%awards_join}}');
	    $this->addForeignKey('KEY_AWARDS_JOIN_AWARD_ID', '{{%awards_join}}', 'award_id', '{{%awards_activity}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_121422_add_table_awards_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_121422_add_table_awards_join cannot be reverted.\n";

        return false;
    }
    */
}
