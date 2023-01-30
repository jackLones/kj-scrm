<?php

use yii\db\Migration;

/**
 * Class m210301_023019_add_columns_record_type_to_work_external_contact_follow_record_table
 */
class m210304_029900_add_columns_ignore_add_wechat_tip_to_public_sea_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%public_sea_customer}}', 'ignore_add_wechat_tip', 'tinyint(1) NULL DEFAULT 0 COMMENT \'是否忽略打完电话添加客户微信好友的弹窗提示0：不忽略；1：忽略\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210304_029900_add_columns_ignore_add_wechat_tip_to_public_sea_customer_table cannot be reverted.\n";

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
