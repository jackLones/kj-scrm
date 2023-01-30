<?php

use yii\db\Migration;

/**
 * Class m191114_114351_change_table_interact_reply
 */
class m191114_114351_change_table_interact_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply}}', 'status', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'是否开启，0代表未开启，1代表开启\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_114351_change_table_interact_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_114351_change_table_interact_reply cannot be reverted.\n";

        return false;
    }
    */
}
