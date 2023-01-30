<?php

use yii\db\Migration;

/**
 * Class m200227_062210_change_table_sub_user_authority
 */
class m200227_062210_change_table_sub_user_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addForeignKey('KEY_AUTHOR_SUB_ID', '{{%sub_user_authority}}', 'sub_user_id', '{{%sub_user}}', 'sub_id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_062210_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_062210_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }
    */
}
