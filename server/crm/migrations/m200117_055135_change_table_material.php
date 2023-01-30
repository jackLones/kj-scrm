<?php

use yii\db\Migration;

/**
 * Class m200117_055135_change_table_material
 */
class m200117_055135_change_table_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%material}}', 'attachment_id', 'int(11) unsigned COMMENT \'附件id\'');
	    $this->addColumn('{{%work_material}}', 'attachment_id', 'int(11) unsigned COMMENT \'附件id\'');
	    $this->addForeignKey('KEY_MATERIAL_ATTACHMENT_ID', '{{%material}}', 'attachment_id', '{{%attachment}}', 'id');
	    $this->addForeignKey('KEY_WORK_MATERIAL_ATTACHMENT_ID', '{{%work_material}}', 'attachment_id', '{{%attachment}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_055135_change_table_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_055135_change_table_material cannot be reverted.\n";

        return false;
    }
    */
}
