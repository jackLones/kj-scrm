<?php

use yii\db\Migration;

/**
 * Class m200225_091503_change_table_user_profile
 */
class m200225_091503_change_table_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user_profile}}', 'sex', ' tinyint(1) DEFAULT NULL COMMENT \'性别\' ');
	    $this->addColumn('{{%user_profile}}', 'department', ' varchar(50) DEFAULT NULL COMMENT \'部门\' ');
	    $this->addColumn('{{%user_profile}}', 'position', ' varchar(50) DEFAULT NULL COMMENT \'职务\' ');
	    $this->addColumn('{{%user_profile}}', 'company_name', ' varchar(50) DEFAULT NULL COMMENT \'企业名称\' ');
	    $this->addColumn('{{%user_profile}}', 'company_logo', ' varchar(100) DEFAULT NULL COMMENT \'企业logo\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200225_091503_change_table_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_091503_change_table_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
