<?php

use yii\db\Migration;

/**
 * Class m200114_011737_change_table_work_welcome
 */
class m200114_011737_change_table_work_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_welcome}}', 'source', 'tinyint(1) DEFAULT 0 COMMENT \'来源0 系统 1渠道活码\' AFTER `type` ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200114_011737_change_table_work_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200114_011737_change_table_work_welcome cannot be reverted.\n";

        return false;
    }
    */
}
