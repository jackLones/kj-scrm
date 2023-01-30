<?php

use yii\db\Migration;

/**
 * Class m200217_071451_change_table_work_corp
 */
class m200217_071451_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropColumn('{{%work_corp}}', 'sync_user_time');
	    $this->dropColumn('{{%work_corp}}', 'last_tag_time');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200217_071451_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200217_071451_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
