<?php

use yii\db\Migration;

/**
 * Class m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table
 */
class m210315_029900_add_columns_monthly_money_to_dialout_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dialout_config}}', 'monthly_money', 'decimal(12,2) DEFAULT NULL COMMENT \'月租\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210315_029900_add_columns_monthly_money_to_dialout_config_table cannot be reverted.\n";

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
