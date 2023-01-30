<?php

use yii\db\Migration;

/**
 * Class m200819_055815_change_table_work_user
 */
class m200819_055815_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_user}}', 'dimission_time', 'int(11) unsigned DEFAULT \'0\'  COMMENT \'离职时间\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200819_055815_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200819_055815_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
