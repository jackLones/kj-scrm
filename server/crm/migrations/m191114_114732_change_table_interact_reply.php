<?php

use yii\db\Migration;

/**
 * Class m191114_114732_change_table_interact_reply
 */
class m191114_114732_change_table_interact_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%interact_reply}}', 'push_num', 'int(11) unsigned DEFAULT 0 COMMENT \'推送人数\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_114732_change_table_interact_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_114732_change_table_interact_reply cannot be reverted.\n";

        return false;
    }
    */
}
