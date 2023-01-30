<?php

use yii\db\Migration;

/**
 * Class m200521_014316_change_table_application_sign
 */
class m200521_014316_change_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%application_sign}}', 'come_from', 'tinyint(1) DEFAULT \'0\' COMMENT \'0行业集成1小猪智慧店铺2有赞3淘宝/天猫\'');
		$this->addColumn("{{%application_sign}}", "third_id", "int(11)  DEFAULT '0' COMMENT '有赞或淘宝关联表的id' after `come_from`");
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_014316_change_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_014316_change_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
