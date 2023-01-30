<?php

use yii\db\Migration;

/**
 * Class m210203_065357_change_table_work_sop
 */
class m210203_065357_change_table_work_sop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_sop}}', 'create_user_id', 'int(11) DEFAULT \'0\' COMMENT \'创建者员工id\' after `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210203_065357_change_table_work_sop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210203_065357_change_table_work_sop cannot be reverted.\n";

        return false;
    }
    */
}
