<?php

use yii\db\Migration;

/**
 * Class m200417_013711_change_table_fans
 */
class m200417_013711_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%fans}}", "follow_status", "tinyint(1) DEFAULT 0 COMMENT '跟进状态' AFTER `last_time`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200417_013711_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200417_013711_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
