<?php

use yii\db\Migration;

/**
 * Class m200601_012456_change_table_application_sign
 */
class m200601_012456_change_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%application_sign}}', 'come_from', 'tinyint(1) DEFAULT 0 COMMENT \'0行业集成1小猪智慧店铺2有赞3淘宝4天猫\' AFTER `sign`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200601_012456_change_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200601_012456_change_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
