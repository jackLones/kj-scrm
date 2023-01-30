<?php

use yii\db\Migration;

/**
 * Class m200529_012906_change_table_work_chat
 */
class m200529_012906_change_table_work_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_chat}}', 'group_id', 'int(11) NOT NULL DEFAULT 0 COMMENT \'所属分组id\'');
	    $this->addColumn('{{%work_chat}}', 'status', 'int(3) NOT NULL DEFAULT 0 COMMENT \'客户群状态 0-正常 1-跟进人离职 2-离职继承中 3-离职继承完成\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_012906_change_table_work_chat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_012906_change_table_work_chat cannot be reverted.\n";

        return false;
    }
    */
}
