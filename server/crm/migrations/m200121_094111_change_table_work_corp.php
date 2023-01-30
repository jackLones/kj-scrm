<?php

use yii\db\Migration;

/**
 * Class m200121_094111_change_table_work_corp
 */
class m200121_094111_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_corp}}', 'suite_id', 'int(11) unsigned COMMENT \'应用ID\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200121_094111_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200121_094111_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
