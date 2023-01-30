<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_guide_change_log}}`.
 */
class m210126_012044_create_shop_customer_guide_change_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_guide_change_log}}', [
            'id'          => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'     => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'      => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客id'),
            'guide_id'    => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('导购id'),
            'store_id'    => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('门店id'),
            'operator_id' => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('操作员工id'),
            'type'        => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('操作类型：0解绑  1绑定'),
            'add_time'    => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_GUIDE_CHANGE_LOG_CORP_CUS_ID', '{{%shop_customer_guide_change_log}}', 'corp_id,cus_id');
        $this->addCommentOnTable('{{%shop_customer_guide_change_log}}', '顾客导购关系变更日志表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_guide_change_log}}');
        return false;
    }
}
