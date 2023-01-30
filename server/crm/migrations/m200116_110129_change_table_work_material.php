<?php

use yii\db\Migration;

/**
 * Class m200116_110129_change_table_work_material
 */
class m200116_110129_change_table_work_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_material}}', 'attachment_id', 'int(11) unsigned COMMENT \'附件id\' AFTER `content_type`');
	    $this->addForeignKey('KEY_WORK_MATERIAL_ATTACHMENT_ID', '{{%work_material}}', 'attachment_id', '{{%attachment}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200116_110129_change_table_work_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200116_110129_change_table_work_material cannot be reverted.\n";

        return false;
    }
    */
}
