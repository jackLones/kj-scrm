<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_rfm_log}}`.
 */
class m210121_060642_create_shop_customer_rfm_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_rfm_log}}', [
            'id'             =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'        =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'         =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客id'),
            'rfm_id'         =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('等级ID'),
            'rfm_name'       =>$this->string(100)->notNUll()->defaultValue('')->comment('等级名称'),
            'before_rfm_id'  =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('之前的等级'),
            'before_rfm_name'=>$this->string(100)->notNUll()->defaultValue('')->comment('之前等级名称'),
            'add_time'       =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_RFM_LOG_CORP_CUS_ID', '{{%shop_customer_rfm_log}}', 'corp_id,cus_id');
        $this->addCommentOnTable('{{%shop_customer_rfm_log}}', '顾客RFM等级变更日志表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_rfm_log}}');
        return false;
    }
}
