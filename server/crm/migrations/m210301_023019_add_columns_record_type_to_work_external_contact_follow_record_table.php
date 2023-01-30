<?php

use yii\db\Migration;

/**
 * Class m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table
 */
class m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_external_contact_follow_record}}', 'record_type', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：手动添加；1：电话记录\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table cannot be reverted.\n";

        return false;
    }
    */
}
