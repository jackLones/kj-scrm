<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_rfm_alias}}`.
 */
class m210121_060118_create_shop_customer_rfm_alias_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_rfm_alias}}', [
            'id'         => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'    =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'rfm_id'     =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('rfm_default的id'),
            'rfm_name'   =>$this->string(100)->notNull()->defaultValue('')->comment('⾃定义名称'),
            'add_time'   =>$this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'=>$this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_RFM_ALIAS_CORP_RFM_ID', '{{%shop_customer_rfm_alias}}', 'corp_id,rfm_id');
        $this->addCommentOnTable('{{%shop_customer_rfm_alias}}', '顾客RFM等级别名表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_rfm_alias}}');
        return false;
    }
}
