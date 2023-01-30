<?php

use yii\db\Migration;

/**
 * Class m191011_120828_change_table_material
 */
class m191011_120828_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 'status', 'tinyint(1) NOT NULL DEFAULT 1 COMMENT \'1可用 0不可用\' ');
	    $this->alterColumn('{{%material}}', 'file_name', 'text COMMENT \'素材名称\' AFTER `article_sort`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191011_120828_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191011_120828_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
