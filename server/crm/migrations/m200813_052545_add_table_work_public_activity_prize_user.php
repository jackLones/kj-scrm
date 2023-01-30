<?php

use yii\db\Migration;

/**
 * Class m200813_052545_add_table_work_public_activity_prize_user
 */
class m200813_052545_add_table_work_public_activity_prize_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%work_public_activity_prize_user}}",[
		    "id"=>$this->primaryKey(11)->unsigned(),
		    "status"=>$this->integer(1)->defaultValue(0)->unsigned()->comment("默认1未发送2已发送"),
		    "order_sn"=>$this->char(60)->unsigned()->comment("红包订单号"),
		    "public_id"=>$this->integer(11)->unsigned()->comment("公众号用户id"),
		    "mobile"=>$this->char(20)->unsigned()->comment("用户留存手机"),
		    "activity_id"=>$this->integer(11)->unsigned()->comment("活动id"),
		    "level"=>$this->integer(1)->unsigned()->comment("奖品等级"),
		    "level_id"=>$this->integer(11)->unsigned()->comment("阶段id"),
		    "region"=>$this->char(60)->unsigned()->comment("地区"),
		    "city"=>$this->char(60)->unsigned()->comment("城市"),
		    "county"=>$this->char(60)->unsigned()->comment("县"),
		    "detail"=>$this->string(255)->unsigned()->comment("详细地址"),
		    "remark"=>$this->string(255)->unsigned()->comment("备注"),
		    "create_time"=>$this->integer(11)->unsigned()->comment("创建时间"),
		    "update_time"=>$this->integer(11)->unsigned()->comment("修改时间"),
	    ],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务宝活动奖品用户领取奖品明细'");
	    $this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_PRIZE_USER', '{{%work_public_activity_prize_user}}', 'activity_id', '{{%work_public_activity}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200813_052545_add_table_work_public_activity_prize_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200813_052545_add_table_work_public_activity_prize_user cannot be reverted.\n";

        return false;
    }
    */
}
