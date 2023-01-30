<?php

use yii\db\Migration;

/**
 * Class m191220_024702_change_table_fans_time_line
 */
class m191220_024702_change_table_fans_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createIndex('KEY_FANS_TIME_LINE_SCENEID', '{{%fans_time_line}}', 'scene_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191220_024702_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191220_024702_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }
    */
}
