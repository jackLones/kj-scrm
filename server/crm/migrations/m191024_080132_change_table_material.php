<?php

use yii\db\Migration;

/**
 * Class m191024_080132_change_table_material
 */
class m191024_080132_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 'news_type', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'1、单图文  2、多图文 \'  AFTER `material_type` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191024_080132_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191024_080132_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
