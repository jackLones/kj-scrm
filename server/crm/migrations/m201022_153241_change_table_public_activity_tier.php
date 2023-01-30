<?php

use yii\db\Migration;

/**
 * Class m201022_153241_change_table_public_activity_tier
 */
class m201022_153241_change_table_public_activity_tier extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_public_activity_tier}}', 'parent', $this->integer(11)->unsigned()->comment("任务宝参与上级id一对一")->after("parent_id"));
	    $this->addColumn('{{%work_public_activity_tier}}', 'create_time', $this->integer(11)->unsigned()->comment("任务宝参与上级id一对一")->after("level"));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_153241_change_table_public_activity_tier cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_153241_change_table_public_activity_tier cannot be reverted.\n";

        return false;
    }
    */
}
