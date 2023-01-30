<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer}}`.
 */
class m210126_091300_create_shop_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer}}', [
            'id'                    =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'               =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'user_id'               =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('员⼯的ID'),
            'external_id'           =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('企微客户主表ID pig_work_external_contact'),
            'sea_customer_id'       =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('⾮企微客户主表ID pig_public_sea_customer'),
            'union_id'              =>$this->string(200)->notNUll()->defaultValue('')->comment('用户union_id'),
            'phone'                 =>$this->string(11)->notNUll()->defaultValue('')->comment('⼿机号（可能不能唯⼀） 可能存在刚开始没有⼿机号的情况，允许为空'),
            'level_id'              =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('等级ID（跟着对应的数据变动更新） 关联等级表ID'),
            'rfm_id'                =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('RFM 平级ID（跟着对应的数据变动更新） 关联RFM 表'),
            'name'                  =>$this->text()->notNUll()->defaultValue('')->comment('顾客昵称 （以⼊库第⼀条为准，后期可以修改）'),
            'true_name'             =>$this->string(100)->notNUll()->defaultValue('')->comment('顾客真实姓名-⼿动编辑，属于顾客管理后期可以自定义的名称)'),
            'last_interactive_time' =>$this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('最后互动时间'),
            'interactive_count'     =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('总互动次数'),
            'frequency_msg'         =>$this->decimal(10,2)->unsigned()->notNUll()->defaultValue('0.00')->comment('会话频率'),
            'recency_msg'           =>$this->decimal(10,2)->unsigned()->notNUll()->defaultValue('0.00')->comment('会话近度'),
            'last_consumption_time' =>$this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('最后消费时间'),
            'consumption_count'     =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('总消费次数'),
            'frequency_shopping'    =>$this->decimal(10,2)->unsigned()->notNUll()->defaultValue('0.00')->comment('消费频率'),
            'recency_shopping'      =>$this->decimal(10,2)->unsigned()->notNUll()->defaultValue('0.00')->comment('消费近度'),
            'amount'                =>$this->decimal(10,2)->unsigned()->notNUll()->defaultValue('0.00')->comment('消费金额'),
            'is_del'                =>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('0正常 1删除'),
            'add_time'              =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'           =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_CORP_ID', '{{%shop_customer}}', 'corp_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_USER_ID', '{{%shop_customer}}', 'user_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_EXTERNAL_ID', '{{%shop_customer}}', 'external_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_SEA_CUSTOMER_ID', '{{%shop_customer}}', 'sea_customer_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_PHONE', '{{%shop_customer}}', 'phone');
        $this->createIndex('KEY_SHOP_CUSTOMER_LEVEL_ID', '{{%shop_customer}}', 'level_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_RFM_ID', '{{%shop_customer}}', 'rfm_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_TRUE_NAME', '{{%shop_customer}}', 'true_name');
        $this->createIndex('KEY_SHOP_CUSTOMER_INTER_COUNT', '{{%shop_customer}}', 'interactive_count');
        $this->createIndex('KEY_SHOP_CUSTOMER_CON_COUNT', '{{%shop_customer}}', 'consumption_count');
        $this->createIndex('KEY_SHOP_CUSTOMER_AMOUNT', '{{%shop_customer}}', 'amount');
        $this->createIndex('KEY_SHOP_CUSTOMER_IS_DEL', '{{%shop_customer}}', 'is_del');
        $this->addCommentOnTable('{{%shop_customer}}', '顾客管理表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer}}');
        return false;
    }
}
