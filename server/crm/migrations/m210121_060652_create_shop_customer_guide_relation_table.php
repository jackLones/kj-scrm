<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_guide_relation}}`.
 */
class m210121_060652_create_shop_customer_guide_relation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_guide_relation}}', [
            'id'         =>$this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'    =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'cus_id'     =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('顾客id'),
            'guide_id'   =>$this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('导购的用户ID'),
            'guide_name' =>$this->string(100)->notNUll()->defaultValue('')->comment('导购名'),
            'store_id'   => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('scrm门店ID'),
            'status'     =>$this->tinyInteger(1)->defaultValue(1)->notNUll()->unsigned()->comment('状态：0 解除关系，1正常'),
            'source_type'=>$this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('1扫码形式2消费时候添加的3订单导⼊添加的4⼿动改变0默认'),
            'add_time'   =>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'=>$this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_shop_customer_guide_relation_CORP_ID', '{{%shop_customer_guide_relation}}', 'corp_id');
        $this->createIndex('KEY_shop_customer_guide_relation_CUS_ID', '{{%shop_customer_guide_relation}}', 'cus_id');
        $this->createIndex('KEY_shop_customer_guide_relation_GUIDE_ID', '{{%shop_customer_guide_relation}}', 'guide_id');
        $this->createIndex('KEY_shop_customer_guide_relation_STATUS', '{{%shop_customer_guide_relation}}', 'status');
        $this->createIndex('KEY_shop_customer_guide_relation_SOURCE_TYPE', '{{%shop_customer_guide_relation}}', 'source_type');
        $this->addCommentOnTable('{{%shop_customer_guide_relation}}', '顾客导购关系');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_guide_relation}}');
        return false;
    }
}
