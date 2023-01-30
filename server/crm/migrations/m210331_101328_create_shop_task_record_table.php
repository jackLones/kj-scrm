<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_task_record}}`.
 */
class m210331_101328_create_shop_task_record_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_task_record}}', [
            'id'        => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'   => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'type'      => $this->tinyInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('任务类型:1 清理企业微信用户 2清理非企业微信用户 3清理订单 '),
            'last_time' => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('最近更新时间'),
            'add_time'  => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('添加时间')
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='电商素材清理任务记录表'");
        $this->createIndex('KEY_SHOP_TASK_RECORD_CORP_ID', '{{%shop_task_record}}', ['corp_id', 'type']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_task_record}}');
    }
}
