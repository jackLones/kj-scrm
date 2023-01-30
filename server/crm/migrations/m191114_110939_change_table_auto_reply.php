<?php

use yii\db\Migration;

/**
 * Class m191114_110939_change_table_auto_reply
 */
class m191114_110939_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%auto_reply}}', 'touch_type');
	    $this->dropColumn('{{%auto_reply}}', 'is_push');
	    $this->dropColumn('{{%auto_reply}}', 'keep_quiet_time');
	    $this->dropColumn('{{%auto_reply}}', 'times_limit');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_110939_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_110939_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
