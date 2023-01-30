<?php

use yii\db\Migration;

/**
 * Class m210408_090101_change_table_external_time_line
 */
class m210408_090101_change_table_external_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%external_time_line}}', 'remark', ' text NOT NULL DEFAULT \'\' COMMENT \'行为相关备注\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210408_090101_change_table_external_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210408_090101_change_table_external_time_line cannot be reverted.\n";

        return false;
    }
    */
}
