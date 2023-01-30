<?php

use yii\db\Migration;

/**
 * Class m200901_033817_change_table_work_contact_way
 */
class m200901_033817_change_table_work_contact_way extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
{
	$this->alterColumn('{{%work_contact_way}}', 'verify_all_day', 'tinyint(1) DEFAULT \'1\' COMMENT \'自动验证1全天开启2分时段\'');
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_033817_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_033817_change_table_work_contact_way cannot be reverted.\n";

        return false;
    }
    */
}
