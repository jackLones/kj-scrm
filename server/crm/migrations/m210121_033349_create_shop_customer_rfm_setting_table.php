<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_rfm_setting}}`.
 */
class m210121_033349_create_shop_customer_rfm_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_rfm_setting}}', [
            'id'                   =>  $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'              =>  $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'consumption_data_open'=>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('消费数据是否开启0:未开启1:已开启'),
            'msg_audit_open'       =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('会话存档数据是否开启0:未开启1:已开启'),
            'msg_allow_time'       =>  $this->smallInteger(4)->unsigned()->notNull()->defaultValue(0)->comment('会话排除时间msg_allow_time分钟'),
            'frequency_type'       =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('1:会话频率2:消费频率'),
            'frequency_value'      =>  $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00')->comment('频率值'),
            'recency_type'         =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('1:会话近度2:消费近度'),
            'recency_value'        =>  $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00')->comment('近度值'),
            'monetary_value'       =>  $this->decimal(10,2)->unsigned()->notNull()->defaultValue('0.00')->comment('消费额度'),
            'add_time'             =>  $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'          =>  $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_RFM_SETTING_CORP_ID', '{{%shop_customer_rfm_setting}}', 'corp_id');
        $this->addCommentOnTable('{{%shop_customer_rfm_setting}}', '顾客RFM设置表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_rfm_setting}}');
        return false;
    }
}
