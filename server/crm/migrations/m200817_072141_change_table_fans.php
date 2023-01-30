<?php

use yii\db\Migration;

/**
 * Class m200817_072141_change_table_fans
 */
class m200817_072141_change_table_fans extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createIndex('KEY_WORK_FANS_UNIONID', '{{%fans}}', 'unionid');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200817_072141_change_table_fans cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200817_072141_change_table_fans cannot be reverted.\n";

        return false;
    }
    */
}
