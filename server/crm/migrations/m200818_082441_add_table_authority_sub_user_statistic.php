<?php

use yii\db\Migration;

/**
 * Class m200818_082441_add_table_authority_sub_user_statistic
 */
class m200818_082441_add_table_authority_sub_user_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%authority_sub_user_statistic}}', [
		    'id'                    => $this->primaryKey(11)->unsigned(),
		    'way_id'                => $this->integer(11)->unsigned()->comment('渠道二维码ID'),
		    'user_id'               => $this->integer(11)->unsigned()->comment('企业成员id'),
		    'increase_cnt'       => $this->integer(11)->defaultValue(0)->unsigned()->comment('净增客户数'),
		    'new_contact_cnt'       => $this->integer(11)->defaultValue(0)->unsigned()->comment('新增客户数'),
		    'delete_cnt'            => $this->integer(11)->defaultValue(0)->unsigned()->comment('员工删除的客户数'),
		    'negative_feedback_cnt' => $this->integer(11)->defaultValue(0)->unsigned()->comment('删除/拉黑成员的客户数'),
		    'data_time'             => $this->char(16)->comment('统计时间'),
		    'is_month'              => $this->tinyInteger(1)->defaultValue(0)->comment('0:按天，1、按月,2按周'),
		    'group_id'              => $this->integer(11)->defaultValue(0)->comment('分组id'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'权限用户范围渠道活码统计\'');
	    $this->addForeignKey('KEY_WORK_CONTACT_SUB_USER_STATISTIC', '{{%authority_sub_user_statistic}}', 'way_id', '{{%work_contact_way}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200818_082441_add_table_authority_sub_user_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_082441_add_table_authority_sub_user_statistic cannot be reverted.\n";

        return false;
    }
    */
}
