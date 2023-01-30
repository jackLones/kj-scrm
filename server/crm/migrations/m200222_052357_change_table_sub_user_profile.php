<?php

use yii\db\Migration;

/**
 * Class m200222_052357_change_table_sub_user_profile
 */
class m200222_052357_change_table_sub_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%sub_user_profile}}', 'company_name', ' varchar(50) DEFAULT NULL COMMENT \'企业名称\' AFTER `position` ');
		$this->addColumn('{{%sub_user_profile}}', 'company_logo', ' varchar(100) DEFAULT NULL COMMENT \'企业logo\' AFTER `position` ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_052357_change_table_sub_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_052357_change_table_sub_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
