<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_operation_log}}`.
 */
class m210121_060723_create_shop_operation_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_operation_log}}', [
            'id'            =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'       =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'table_name'    =>$this->string(100)->notNull()->defaultValue('')->comment('表名'),
            'fields_name'   =>$this->string(100)->notNUll()->defaultValue('')->comment('字段名'),
            'primary_key_id'=>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('对应主键id'),
            'old_value'     =>$this->string(100)->notNUll()->defaultValue('')->comment('旧值'),
            'new_value'     =>$this->string(100)->notNUll()->defaultValue('')->comment('新值'),
            'remarks'       =>$this->string(100)->notNUll()->defaultValue('')->comment('备注'),
            'operator_uid'  =>$this->integer(11)->defaultValue(0)->notNUll()->comment('操作⼈ID'),
            'add_time'      =>$this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('操作时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_OPERATION_LOG_CORP_ID', '{{%shop_operation_log}}', 'corp_id');
        $this->createIndex('KEY_SHOP_OPERATION_LOG_TABLE_NAME', '{{%shop_operation_log}}', 'table_name');
        $this->createIndex('KEY_SHOP_OPERATION_LOG_FIELDS_NAME', '{{%shop_operation_log}}', 'fields_name');
        $this->createIndex('KEY_SHOP_OPERATION_LOG_PRIMARY_KEY_ID', '{{%shop_operation_log}}', 'primary_key_id');
        $this->createIndex('KEY_SHOP_OPERATION_LOG_OPERATOR_UID', '{{%shop_operation_log}}', 'operator_uid');
        $this->addCommentOnTable('{{%shop_operation_log}}', '操作日志表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_operation_log}}');
        return false;
    }
}
