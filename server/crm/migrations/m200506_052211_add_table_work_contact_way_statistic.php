<?php

use yii\db\Migration;

/**
 * Class m200506_052211_add_table_work_contact_way_statistic
 */
class m200506_052211_add_table_work_contact_way_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_statistic}}', [
			'id'                    => $this->primaryKey(11)->unsigned(),
			'way_id'                => $this->integer(11)->unsigned()->comment('渠道二维码ID'),
			'new_contact_cnt'       => $this->integer(11)->unsigned()->comment('新增客户数'),
			'negative_feedback_cnt' => $this->integer(11)->unsigned()->comment('删除/拉黑成员的客户数'),
			'data_time'             => $this->char(16)->comment('统计时间'),
			'is_month'              => $this->tinyInteger(1)->defaultValue(0)->comment('0:按天，1、按月'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码统计表\'');
		$this->addForeignKey('KEY_WORK_CONTACT_WAY_STATISTIC_WAY_ID', '{{%work_contact_way_statistic}}', 'way_id', '{{%work_contact_way}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200506_052211_add_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200506_052211_add_table_work_contact_way_statistic cannot be reverted.\n";

        return false;
    }
    */
}
