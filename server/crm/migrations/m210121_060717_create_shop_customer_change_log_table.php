<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_change_log}}`.
 */
class m210121_060717_create_shop_customer_change_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_change_log}}', [
            'id'          =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'     =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'      =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客id'),
            'table_name'  =>$this->string(100)->notNull()->defaultValue('')->comment('日志表名'),
            'log_id'      =>$this->string(100)->notNUll()->defaultValue('')->comment('对应日志表记录id,多条逗号隔开'),
            'title'       =>$this->string(100)->notNull()->defaultValue('')->comment('变更事件 例:消费/评级/导购'),
            'type'        =>$this->string(100)->notNull()->defaultValue('')->comment('事件类型 例:线下/淘宝'),
            'description' =>$this->text()->notNull()->defaultValue('')->comment('变更具体内容'),
            'add_time'    =>$this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('变更时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_CHANGE_LOG_CORP_CUS_ID', '{{%shop_customer_change_log}}', 'corp_id,cus_id');
        $this->addCommentOnTable('{{%shop_customer_change_log}}', '顾客信息变更记录');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_change_log}}');
        return false;
    }
}
