<?php

use yii\db\Migration;

/**
 * Class m191016_054126_change_table_wx_authorize_info
 */
class m191016_054126_change_table_wx_authorize_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wx_authorize_info}}', 'industry', 'varchar(50) NOT NULL COMMENT \'行业\' AFTER `signature` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_054126_change_table_wx_authorize_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_054126_change_table_wx_authorize_info cannot be reverted.\n";

        return false;
    }
    */
}
