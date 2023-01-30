<?php

use yii\db\Migration;

/**
 * Class m200318_021945_change_table_awards_records
 */
class m200318_021945_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%awards_records}}', 'award_name', 'char(100)  COMMENT \'奖品名称\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_021945_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_021945_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
