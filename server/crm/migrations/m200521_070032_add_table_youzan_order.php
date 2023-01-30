<?php

use yii\db\Migration;

/**
 * Class m200521_070032_add_table_youzan_order
 */
class m200521_070032_add_table_youzan_order extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$sql
			= <<<SQL
CREATE TABLE {{%youzan_order}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
	`kdt_id` int(11) NOT NULL DEFAULT '0' COMMENT '有赞店铺id',
  `tid` varchar(100) NOT NULL DEFAULT '' COMMENT '有赞订单号',
  `type` int(5) NOT NULL DEFAULT 0 COMMENT '主订单类型 0:普通订单; 1:送礼订单; 2:代付; 3:分销采购单; 4:赠品; 5:心愿单; 6:二维码订单; 7:合并付货款; 8:1分钱实名认证; 9:品鉴; 10:拼团; 15:返利; 35:酒店; 40:外卖; 41:堂食点餐; 46:外卖买单; 51:全员开店; 61:线下收银台订单; 71:美业预约单; 72:美业服务单; 75:知识付费; 81:礼品卡; 100:批发',
  `pay_type_str` varchar(50) NOT NULL DEFAULT '' COMMENT '支付类型。取值范围：WEIXIN(微信自有支付) WEIXIN_DAIXIAO(微信代销支付) ALIPAY(支付宝支付) BANKCARDPAY(银行卡支付) PEERPAY(代付) CODPAY(货到付款) BAIDUPAY(百度钱包支付) PRESENTTAKE(直接领取赠品) COUPONPAY(优惠券/码全额抵扣) BULKPURCHASE(来自分销商的采购) MERGEDPAY(合并付货款) ECARD(有赞E卡支付) PURCHASE_PAY (采购单支付) MARKPAY (标记收款) OFCASH (现金支付) PREPAIDCARD (储值卡余额支付)ENCHASHMENT_GIFT_CARD(礼品卡支付)',
  `status` varchar(100) NOT NULL DEFAULT '' COMMENT '主订单状态 WAIT_BUYER_PAY （等待买家付款，定金预售描述：定金待付、等待尾款支付开始、尾款待付）； TRADE_PAID（订单已支付 ）； WAIT_CONFIRM（待确认，包含待成团、待接单等等。即：买家已付款，等待成团或等待接单）； WAIT_SELLER_SEND_GOODS（等待卖家发货，即：买家已付款）； WAIT_BUYER_CONFIRM_GOODS (等待买家确认收货，即：卖家已发货) ； TRADE_SUCCESS（买家已签收以及订单成功）； TRADE_CLOSED（交易关闭）',
	`created` int(11) NOT NULL DEFAULT 0 COMMENT '订单创建时间',
	`update_time` int(11) NOT NULL DEFAULT 0 COMMENT '订单更新时间',
	`pay_time` int(11) NOT NULL DEFAULT 0 COMMENT '订单支付时间',
	`is_payed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否支付1是0否',
	`is_member` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否会员订单1是0否',
	`refund_state` int(3) NOT NULL DEFAULT 0 COMMENT '退款状态 0:未退款; 1:部分退款中; 2:部分退款成功; 11:全额退款中; 12:全额退款成功',
	`payment` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '最终支付价格',
	`total_fee` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '优惠前商品总价',
	`post_fee` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '邮费',
	`outer_transactions` varchar(100) DEFAULT '' COMMENT '外部支付单号',
	`cashier_name` varchar(100) DEFAULT '' COMMENT '收银员名字',
	`fans_nickname` varchar(100) DEFAULT '' COMMENT '粉丝昵称',
	`fans_id` int(11) DEFAULT 0 COMMENT '粉丝id',
	`fans_type` int(11) DEFAULT 0 COMMENT '粉丝类型0:未知、1:微信自有粉丝; 9:代销粉丝,2:微博,105:,知乎,128: 有赞精选app,188: QQ,312:腾讯云,736:支付宝,1180:百度,1181:今日头条,1590:微信广告,56473:陌陌 ,59011:线下微信支付产生的粉丝,59465:线下支付宝支付,4591:有人,16940: 快手 ,26879:上鱼,6227:精选小程序,61489:虎牙',
	`buyer_phone` varchar(50) DEFAULT '' COMMENT '买家手机号',
	`outer_user_id` varchar(100) DEFAULT '' COMMENT '微信weixin_openid（支付人）',
	`yz_open_id` varchar(100) DEFAULT '' COMMENT '有赞对外统一openId（支付人）',
	`receiver_tel` varchar(50) DEFAULT '' COMMENT '收货人手机号',
	`receiver_name` varchar(100) DEFAULT '' COMMENT '收货人姓名',
	`delivery_province` varchar(100) DEFAULT '' COMMENT '省',
	`delivery_city` varchar(100) DEFAULT '' COMMENT '市',
	`delivery_district` varchar(100) DEFAULT '' COMMENT '区',
	`delivery_address` varchar(500) DEFAULT '' COMMENT '详细地址',
	`buyer_message` varchar(500) DEFAULT '' COMMENT '订单买家留言',
	`trade_memo` varchar(500) DEFAULT '' COMMENT '订单商家备注',
	`star` int(2) DEFAULT 0 COMMENT '订单标星等级 0-5',
  `extrainfo` text CHARACTER SET utf8 NOT NULL COMMENT '额外信息',
  `third_data` text CHARACTER SET utf8 NOT NULL COMMENT '第三方商品内容title,num,price（原价）,total_fee',
  `time` int(11) NOT NULL DEFAULT 0 COMMENT '订单同步时间',
  PRIMARY KEY (`id`),
  KEY `KEY_YOUZAN_ORDER_UID` (`uid`),
  KEY `KEY_YOUZAN_ORDER_KDTID` (`kdt_id`),
  KEY `KEY_YOUZAN_ORDER_TID` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='有赞订单表';
SQL;

		$this->execute($sql);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200521_070032_add_table_youzan_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200521_070032_add_table_youzan_order cannot be reverted.\n";

        return false;
    }
    */
}
