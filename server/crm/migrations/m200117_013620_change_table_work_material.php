<?php

use yii\db\Migration;

/**
 * Class m200117_013620_change_table_work_material
 */
class m200117_013620_change_table_work_material extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey('KEY_WORK_MATERIAL_ATTACHMENT_ID', '{{%work_material}}');
	    $this->dropColumn('{{%work_material}}', 'attachment_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_013620_change_table_work_material cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_013620_change_table_work_material cannot be reverted.\n";

        return false;
    }
    */
}
