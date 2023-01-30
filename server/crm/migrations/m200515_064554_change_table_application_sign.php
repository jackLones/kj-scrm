<?php

use yii\db\Migration;

/**
 * Class m200515_064554_change_table_application_sign
 */
class m200515_064554_change_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn("{{%application_sign}}", "username", "char(100) NOT NULL DEFAULT '' COMMENT '店铺名称' after `come_from`");
		$this->addColumn("{{%application_sign}}", "status", "tinyint(1)  DEFAULT '1' COMMENT '1已授权2未授权' after `come_from`");
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200515_064554_change_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200515_064554_change_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
