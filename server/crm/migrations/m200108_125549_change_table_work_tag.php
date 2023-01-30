<?php

use yii\db\Migration;

/**
 * Class m200108_125549_change_table_work_tag
 */
class m200108_125549_change_table_work_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag}}', 'group_id', 'int(11) DEFAULT "0" COMMENT \'分组id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200108_125549_change_table_work_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200108_125549_change_table_work_tag cannot be reverted.\n";

        return false;
    }
    */
}
