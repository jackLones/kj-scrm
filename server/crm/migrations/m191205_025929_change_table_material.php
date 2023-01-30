<?php

use yii\db\Migration;

/**
 * Class m191205_025929_change_table_material
 */
class m191205_025929_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%material}}', 'file_length', 'int(11) DEFAULT \'0\' COMMENT \'素材大小\'');
	    $this->addColumn('{{%material}}', 'media_width', 'char(8) DEFAULT \'\' COMMENT \'素材宽度 \'  AFTER `file_name` ');
	    $this->addColumn('{{%material}}', 'media_height', 'char(8) DEFAULT \'\' COMMENT \'素材高度 \'  AFTER `media_width` ');
	    $this->addColumn('{{%material}}', 'media_duration', 'char(8) DEFAULT \'\' COMMENT \'素材时长秒 \'  AFTER `media_height` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191205_025929_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191205_025929_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
