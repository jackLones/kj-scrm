<?php

use yii\db\Migration;

/**
 * Class m200324_052548_change_table_awards_activity
 */
class m200324_052548_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%awards_activity}}', 'update_time', 'timestamp NOT NULL COMMENT \'修改时间\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200324_052548_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_052548_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
