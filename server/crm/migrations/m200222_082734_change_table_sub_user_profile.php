<?php

use yii\db\Migration;

/**
 * Class m200222_082734_change_table_sub_user_profile
 */
class m200222_082734_change_table_sub_user_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->dropColumn('{{%sub_user_profile}}', 'company_logo');
		$this->dropColumn('{{%sub_user_profile}}', 'company_name');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_082734_change_table_sub_user_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_082734_change_table_sub_user_profile cannot be reverted.\n";

        return false;
    }
    */
}
