<?php

use yii\db\Migration;

/**
 * Class m200115_085003_change_table_attachment_group
 */
class m200115_085003_change_table_attachment_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%attachment}}', 'file_name', 'varchar(128) DEFAULT \'\' COMMENT \'附件名称\'');
	    $this->alterColumn('{{%attachment}}', 'file_content_type', 'varchar(128) DEFAULT \'\' COMMENT \'附件类型\'');
	    $this->alterColumn('{{%work_material}}', 'content_type', 'varchar(128) DEFAULT \'\' COMMENT \'素材类型\'');
	    $this->addColumn('{{%attachment_group}}', 'sort', 'int(11) unsigned DEFAULT \'0\' COMMENT \'分组排序\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200115_085003_change_table_attachment_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200115_085003_change_table_attachment_group cannot be reverted.\n";

        return false;
    }
    */
}
