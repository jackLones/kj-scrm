<?php

use yii\db\Migration;

/**
 * Class m200122_013545_change_table_work_tag_group
 */
class m200122_013545_change_table_work_tag_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag_group}}', 'sort', 'int(11) unsigned DEFAULT 0 COMMENT \'排序\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_013545_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_013545_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }
    */
}
