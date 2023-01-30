<?php

use yii\db\Migration;

/**
 * Class m200627_082506_change_table_awards_activity
 */
class m200627_082506_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_activity}}', 'prize_send_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'奖品发放类型：1、活动期间，2、活动结束\' AFTER `share_setting`');
	    $this->addColumn('{{%awards_activity}}', 'sex_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'性别类型：1、不限制，2、男性，3、女性，4、未知\' AFTER `prize_send_type`');
	    $this->addColumn('{{%awards_activity}}', 'area_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'区域类型：1、不限制，2、部分地区\' AFTER `sex_type`');
	    $this->addColumn('{{%awards_activity}}', 'area_data', 'text COMMENT \'区域数据\' AFTER `area_type`');
	    $this->addColumn('{{%awards_activity}}', 'tag_ids', 'varchar(250) NOT NULL DEFAULT \'\' COMMENT \'给客户打的标签\' AFTER `area_data`');
	    $this->alterColumn('{{%awards_join}}', 'openid', 'varchar(50) NOT NULL DEFAULT \'\' COMMENT \'参与者身份openid\'');
	    $this->addColumn('{{%awards_list}}', 'prize_type', 'tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'奖品类型：0、实物，1、红包\' AFTER `description`');
	    $this->addColumn('{{%awards_list}}', 'amount', 'decimal(12,2) NOT NULL DEFAULT \'0.00\' COMMENT \'红包金额\' AFTER `prize_type`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200627_082506_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200627_082506_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
