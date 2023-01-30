<?php

use yii\db\Migration;

/**
 * Class m200618_054223_change_fission_table
 */
class m200618_054223_change_fission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fission}}', 'prize_type', 'tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'奖品类型：0、实物，1、红包\' AFTER `complete_num`');
	    $this->addColumn('{{%fission}}', 'prize_send_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'奖品发放类型：1、活动期间，2、活动结束\' AFTER `prize_type`');
	    $this->addColumn('{{%fission}}', 'help_limit', 'int(10) NOT NULL DEFAULT \'0\' COMMENT \'好友助力次数限制\' AFTER `prize_send_type`');
	    $this->addColumn('{{%fission}}', 'sex_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'性别类型：1、不限制，2、男性，3、女性，4、未知\' AFTER `help_limit`');
	    $this->addColumn('{{%fission}}', 'area_type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'区域类型：1、不限制，2、部分地区\' AFTER `sex_type`');
	    $this->addColumn('{{%fission}}', 'area_data', 'text COMMENT \'区域数据\' AFTER `area_type`');
	    $this->addColumn('{{%fission}}', 'tag_ids', 'varchar(250) NOT NULL DEFAULT \'\' COMMENT \'给客户打的标签\' AFTER `area_data`');
	    $this->addColumn('{{%fission_join}}', 'amount', 'decimal(12,2) NOT NULL DEFAULT \'0.00\' COMMENT \'红包金额\' AFTER `fission_num`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200618_054223_change_fission_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200618_054223_change_fission_table cannot be reverted.\n";

        return false;
    }
    */
}
