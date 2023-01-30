<?php

use yii\db\Migration;

/**
 * Class m200608_123105_change_table_work_group_sending
 */
class m200608_123105_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%work_group_sending}}', 'attribute', 'text COMMENT \'高级属性字段\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200608_123105_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200608_123105_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
