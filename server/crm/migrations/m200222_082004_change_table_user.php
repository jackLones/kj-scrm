<?php

use yii\db\Migration;

/**
 * Class m200222_082004_change_table_user
 */
class m200222_082004_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%user}}', 'company_name', ' varchar(50) DEFAULT NULL COMMENT \'企业名称\' ');
		$this->addColumn('{{%user}}', 'company_logo', ' varchar(100) DEFAULT NULL COMMENT \'企业logo\' ');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_082004_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_082004_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
