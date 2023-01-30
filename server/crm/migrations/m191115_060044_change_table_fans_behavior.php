<?php

use yii\db\Migration;

/**
 * Class m191115_060044_change_table_fans_behavior
 */
class m191115_060044_change_table_fans_behavior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans_behavior}}', 'scene_id', 'int(11) unsigned DEFAULT 0 COMMENT \'参数二维码id\'');
	    $this->addColumn('{{%fans_time_line}}', 'scene_id', 'int(11) unsigned DEFAULT 0 COMMENT \'参数二维码id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191115_060044_change_table_fans_behavior cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191115_060044_change_table_fans_behavior cannot be reverted.\n";

        return false;
    }
    */
}
