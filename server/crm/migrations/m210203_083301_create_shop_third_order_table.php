<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_third_order}}`.
 */
class m210203_083301_create_shop_third_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_third_order}}', [
            'id'                  => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'             => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'shop_api_key'        => $this->string(100)->notNUll()->defaultValue('')->comment('对接的key, 冗余'),
            'source'              => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单来源:0 手工录入 1小猪电商 2淘宝 3有赞 '),
            'order_no'            => $this->string(100)->notNUll()->defaultValue('')->comment('唯一订单号'),
            'payment_amount'      => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('订单实际⽀付⾦额'),
            'refund_amount'       => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('退款金额'),
            'post_fee'            => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('邮费'),
            'use_points'          => $this->decimal(10, 2)->unsigned()->defaultValue('0.00')->notNull()->comment('积分'),
            'adjust_fee'          => $this->decimal(10, 2)->defaultValue('0.00')->notNull()->comment('手工调整金额'),
            'send_time'           => $this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('发货时间'),
            'end_time'            => $this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('订单完成时间'),
            'payment_method'      => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('支付方式 0 未知 1支付宝 2微信 3系统余额 4银行卡 等'),
            'payment_method_name' => $this->string(100)->notNUll()->defaultValue('')->comment('支付方式名称:微信 建设银行 易宝等'),
            'guide_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('归属导购员ID'),
            'store_id'            => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('第三方系统的⻔店ID'),
            'store_name'          => $this->string(200)->notNUll()->defaultValue('')->comment('第三方系统店铺名称'),
            'scrm_store_id'       => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('scrm 门店ID'),
            'pay_time'            => $this->timestamp()->notNUll()->defaultValue('0000-00-00 00:00:00')->comment('⽀付时间'),
            'buy_name'            => $this->string(150)->notNUll()->defaultValue('')->comment('购买⼈姓名'),
            'buy_phone'           => $this->string(20)->notNUll()->defaultValue('')->comment('购买⼈⼿机号'),
            'receiver_name'       => $this->string(150)->notNUll()->defaultValue('')->comment('收货⼈姓名'),
            'receiver_phone'      => $this->string(20)->notNUll()->defaultValue('')->comment('收货⼈⼿机号'),
            'receiver_zip'        => $this->string(100)->notNUll()->defaultValue('')->comment('收件人邮编，非必填'),
            'receiver_state'      => $this->string(100)->notNUll()->defaultValue('')->comment('收件人省份'),
            'receiver_city'       => $this->string(100)->notNUll()->defaultValue('')->comment('收件人城市'),
            'receiver_district'   => $this->string(100)->notNUll()->defaultValue('')->comment('收件人区县'),
            'receiver_town'       => $this->string(100)->notNUll()->defaultValue('')->comment('收件人街道，非必填'),
            'receiver_address'    => $this->string(200)->notNUll()->defaultValue('')->comment('详细地址，不包含省市区的地址'),
            'union_id'            => $this->string(200)->notNUll()->defaultValue('')->comment('用户union_id'),
            'status'              => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('支付状态: 1正常 2退款'),
            'ext_field'           => $this->text()->notNUll()->defaultValue('')->comment('第三方自定义字段'),
            'order_type'          => $this->tinyInteger(1)->unsigned()->notNUll()->defaultValue(0)->comment('订单类型 0:普通,1:代付,2:送礼,3:分销,4:活动,5:批发,6:拼团,7:预售,10:预约,11:选购,50:砍价,51:人气夺宝,53:秒杀,55:降价拍,56:抽奖,57:摇一摇,58:微聚力,59:拆礼盒,61:集字游戏,62:摇钱树游戏,63:竞价,64:扫码,65:限时折扣,'),
            'add_time'            => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'         => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_THR_O_CORP_ID', '{{%shop_third_order}}', 'corp_id');
        $this->createIndex('KEY_SHOP_THR_O_KEY', '{{%shop_third_order}}', 'shop_api_key');
        $this->createIndex('KEY_SHOP_THR_O_NO', '{{%shop_third_order}}', 'order_no');
        $this->createIndex('KEY_SHOP_THR_O_SOURCE', '{{%shop_third_order}}', 'source');
        $this->createIndex('KEY_SHOP_THR_O_BUY_PHONE', '{{%shop_third_order}}', 'buy_phone');
        $this->createIndex('KEY_SHOP_THR_O_RE_PHONE', '{{%shop_third_order}}', 'receiver_phone');
        $this->createIndex('KEY_SHOP_THR_O_GUIDE_ID', '{{%shop_third_order}}', 'guide_id');
        $this->createIndex('KEY_SHOP_THR_O_STORE_ID', '{{%shop_third_order}}', 'scrm_store_id');
        $this->createIndex('KEY_SHOP_THR_O_PAY_TIME', '{{%shop_third_order}}', 'pay_time');
        $this->createIndex('KEY_SHOP_THR_O_STATUS', '{{%shop_third_order}}', 'status');
        $this->createIndex('KEY_SHOP_THR_O_UPDATE', '{{%shop_third_order}}', 'update_time');
        $this->createIndex('KEY_SHOP_THR_O_ADD','{{%shop_third_order}}', 'add_time');
        $this->addCommentOnTable('{{%shop_third_order}}', '第三方订单列表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_third_order}}');
        return false;
    }
}
