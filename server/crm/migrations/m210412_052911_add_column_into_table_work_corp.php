<?php

use yii\db\Migration;

/**
 * Class m210412_052911_add_column_into_table_work_crop
 */
class m210412_052911_add_column_into_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     *
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_corp}}', 'important_customer_recycle_switch', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'客户导入分配超时回收用户开关 0 关闭 1 开启\'');
        $this->addColumn('{{%work_corp}}', 'important_customer_recycle_time', 'int(11) unsigned DEFAULT 7 COMMENT \'客户导入分配超时天数，默认7天\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_corp}}', 'important_customer_recycle_switch');
        $this->dropColumn('{{%work_corp}}', 'important_customer_recycle_time');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210412_052911_add_column_into_table_work_crop cannot be reverted.\n";

        return false;
    }
    */
}
