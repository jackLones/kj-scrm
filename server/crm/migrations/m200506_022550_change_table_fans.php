<?php

use yii\db\Migration;

/**
 * Class m200506_022550_change_table_fans
 */
class m200506_022550_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn("{{%fans}}", "follow_id", "int(11) unsigned DEFAULT NULL COMMENT '状态id'");
		$this->addForeignKey('KEY_FANS_FOLLOWID', '{{%fans}}', 'follow_id', '{{%follow}}', 'id');
		$this->addColumn("{{%work_external_contact}}", "follow_id", "int(11) unsigned DEFAULT NULL COMMENT '状态id'");
		$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_FOLLOWID', '{{%work_external_contact}}', 'follow_id', '{{%follow}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200506_022550_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200506_022550_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
