<?php

use yii\db\Migration;

/**
 * Class m200901_020730_change_table_work_contact_way
 */
class m200901_020730_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%work_contact_way}}', 'skip_verify', 'tinyint(1) DEFAULT NULL COMMENT \'是否需要验证，1需要 0不需要\'');
		$this->addColumn('{{%work_contact_way}}', 'verify_all_day', 'tinyint(1) DEFAULT \'1\' COMMENT \'自动验证全天开启 1关 2开\' AFTER `skip_verify`');
		$this->addColumn('{{%work_contact_way}}', 'is_limit', 'tinyint(1) DEFAULT \'1\' COMMENT \'员工上限 1关 2开\' AFTER `state`');
		$this->addColumn('{{%work_contact_way}}', 'is_welcome_week', 'tinyint(1) DEFAULT \'1\' COMMENT \'欢迎语时段周 1关 2开\' AFTER `state`');
		$this->addColumn('{{%work_contact_way}}', 'is_welcome_date', 'tinyint(1) DEFAULT \'1\' COMMENT \'欢迎语时段日期 1关 2开\' AFTER `state`');
		$this->addColumn('{{%work_contact_way}}', 'spare_employee', 'varchar(255) COMMENT \'备用员工\' AFTER `state`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_020730_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_020730_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
