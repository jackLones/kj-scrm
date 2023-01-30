<?php

use yii\db\Migration;

/**
 * Class m201009_083417_change_table_fans
 */
class m201009_083417_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans}}', 'activity_id', 'int(11) unsigned DEFAULT 0 COMMENT \'任务宝id\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201009_083417_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201009_083417_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
