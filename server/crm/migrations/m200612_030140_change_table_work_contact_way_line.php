<?php

use yii\db\Migration;

/**
 * Class m200612_030140_change_table_work_contact_way_line
 */
class m200612_030140_change_table_work_contact_way_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_contact_way_line}}', 'create_time', 'timestamp NULL COMMENT \'创建时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200612_030140_change_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200612_030140_change_table_work_contact_way_line cannot be reverted.\n";

        return false;
    }
    */
}
