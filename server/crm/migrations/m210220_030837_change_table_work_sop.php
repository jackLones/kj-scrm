<?php

use yii\db\Migration;

/**
 * Class m210220_030837_change_table_work_sop
 */
class m210220_030837_change_table_work_sop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_sop}}', 'chat_ids', 'varchar(5000) DEFAULT NULL COMMENT \'规则群id\' AFTER `user_ids`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210220_030837_change_table_work_sop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210220_030837_change_table_work_sop cannot be reverted.\n";

        return false;
    }
    */
}
