<?php

use yii\db\Migration;

/**
 * Class m200513_115709_change_table_application_sign
 */
class m200513_115709_change_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%application_sign}}", "come_from", "tinyint(1) DEFAULT 0 COMMENT '0行业集成1店铺管理的智慧店铺' AFTER `sign`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200513_115709_change_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200513_115709_change_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
