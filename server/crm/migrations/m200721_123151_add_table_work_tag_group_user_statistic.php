<?php

use yii\db\Migration;

/**
 * Class m200721_123151_add_table_work_tag_group_user_statistic
 */
class m200721_123151_add_table_work_tag_group_user_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_tag_group_user_statistic}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'user_id'     => $this->integer(11)->unsigned()->comment('员工ID'),
			'pull_id'     => $this->integer(11)->unsigned()->comment('标签拉群的ID'),
			'will_num'    => $this->integer(11)->unsigned()->comment('预计发送人数'),
			'real_num'    => $this->integer(11)->unsigned()->comment('实际发送人数'),
			'has_num'     => $this->integer(11)->unsigned()->comment('已入群人数'),
			'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('发送状态：0未发送1已发送'),
			'push_time'   => $this->integer(11)->unsigned()->comment('发送时间'),
			'create_time' => $this->integer(11)->unsigned()->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'标签拉群成员统计表\'');

		$this->addForeignKey('KEY_WORK_TAG_GROUP_USER_STATISTIC_USER_ID', '{{%work_tag_group_user_statistic}}', 'user_id', '{{%work_user}}', 'id');
		$this->addForeignKey('KEY_WORK_TAG_GROUP_USER_STATISTIC_PULL_ID', '{{%work_tag_group_user_statistic}}', 'pull_id', '{{%work_tag_pull_group}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200721_123151_add_table_work_tag_group_user_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200721_123151_add_table_work_tag_group_user_statistic cannot be reverted.\n";

        return false;
    }
    */
}
