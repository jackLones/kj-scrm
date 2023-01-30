<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_level_log}}`.
 */
class m210121_032400_create_shop_customer_level_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_level_log}}', [
            'id'             => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'        => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客id'),
            'operator_id'    => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('操作人ID'),
            'before_level_id'=> $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('之前的等级'),
            'level_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('等级ID'),
            'name'           => $this->string(100)->notNUll()->defaultValue('')->comment('等级名称'),
            'before_name'    => $this->string(100)->notNUll()->defaultValue('')->comment('之前等级名称'),
            'add_time'       => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_LEVEL_LOG_CORP_CUS_ID', '{{%shop_customer_level_log}}', 'corp_id,cus_id');
        $this->addCommentOnTable('{{%shop_customer_level_log}}', '顾客等级变更日志表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_level_log}}');
        return false;
    }
}
