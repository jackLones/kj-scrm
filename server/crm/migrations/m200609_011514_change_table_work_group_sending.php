<?php

use yii\db\Migration;

/**
 * Class m200609_011514_change_table_work_group_sending
 */
class m200609_011514_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_group_sending}}', 'others', 'text COMMENT \'客户其他筛选字段值\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200609_011514_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200609_011514_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
