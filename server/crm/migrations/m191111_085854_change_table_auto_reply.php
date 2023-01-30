<?php

use yii\db\Migration;

/**
 * Class m191111_085854_change_table_auto_reply
 */
class m191111_085854_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%auto_reply}}', 'tags_select_type');
	    $this->dropColumn('{{%auto_reply}}', 'tags_id');
	    $this->dropColumn('{{%auto_reply}}', 'time_rule');
	    $this->dropColumn('{{%auto_reply}}', 'time_type');
	    $this->dropColumn('{{%auto_reply}}', 'time_num');
	    $this->dropColumn('{{%auto_reply}}', 'push_time');
	    $this->dropColumn('{{%auto_reply}}', 'queue_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191111_085854_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191111_085854_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
