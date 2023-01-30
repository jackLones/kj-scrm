<?php

use yii\db\Migration;

/**
 * Class m200512_053707_change_table_work_user
 */
class m200512_053707_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_user}}', 'day_user_num', 'int(6) NOT NULL DEFAULT 0 COMMENT \'员工单日红包发送次数限制\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200512_053707_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200512_053707_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
