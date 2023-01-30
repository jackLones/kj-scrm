<?php

use yii\db\Migration;

/**
 * Class m190917_031657_change_fans_table_cloumn_targid_list
 */
class m190917_031657_change_fans_table_cloumn_targid_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->renameColumn('{{%fans}}', 'targid_list', 'tagid_list');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190917_031657_change_fans_table_cloumn_targid_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190917_031657_change_fans_table_cloumn_targid_list cannot be reverted.\n";

        return false;
    }
    */
}
