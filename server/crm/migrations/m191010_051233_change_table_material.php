<?php

use yii\db\Migration;

/**
 * Class m191010_051233_change_table_material
 */
class m191010_051233_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 'author_id', 'int(11) unsigned DEFAULT NULL COMMENT \'公众号ID\' AFTER `id`');
	    $this->createIndex('KEY_MATERIAL_AUTHORID', '{{%material}}', 'author_id');
	    $this->addForeignKey('KEY_MATERIAL_AUTHORID', '{{%material}}', 'author_id', '{{%wx_authorize}}', 'author_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191010_051233_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191010_051233_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
