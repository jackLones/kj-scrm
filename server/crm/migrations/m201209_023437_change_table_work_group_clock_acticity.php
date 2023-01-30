<?php

use yii\db\Migration;

/**
 * Class m201209_023437_change_table_work_group_clock_acticity
 */
class m201209_023437_change_table_work_group_clock_acticity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_clock_activity}}', 'short_url', 'varchar(60) DEFAULT \'\' COMMENT \'短连接\'');
	    $this->addColumn('{{%work_group_clock_activity}}', 'url', 'varchar(255) DEFAULT \'\' COMMENT \'原始连接\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201209_023437_change_table_work_group_clock_acticity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201209_023437_change_table_work_group_clock_acticity cannot be reverted.\n";

        return false;
    }
    */
}
