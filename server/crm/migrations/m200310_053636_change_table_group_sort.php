<?php

use yii\db\Migration;

/**
 * Class m200310_053636_change_table_group_sort
 */
class m200310_053636_change_table_group_sort extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%group_sort}}', 'corp_id', 'int(11) unsigned DEFAULT NULL COMMENT \'授权的企业ID\' AFTER `id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_053636_change_table_group_sort cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_053636_change_table_group_sort cannot be reverted.\n";

        return false;
    }
    */
}
