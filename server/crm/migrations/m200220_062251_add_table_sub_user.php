<?php

use yii\db\Migration;

/**
 * Class m200220_062251_add_table_sub_user
 */
class m200220_062251_add_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%sub_user}}', 'status', ' tinyint(2) unsigned DEFAULT \'0\' COMMENT \'状态0未启用1正常2禁用\' AFTER `access_token_expire` ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200220_062251_add_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200220_062251_add_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
