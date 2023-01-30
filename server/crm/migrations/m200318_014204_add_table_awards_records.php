<?php

use yii\db\Migration;

/**
 * Class m200318_014204_add_table_awards_records
 */
class m200318_014204_add_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_records}}', [
			'id'           => $this->primaryKey(11)->unsigned(),
			'award_id'     => $this->integer(11)->unsigned()->comment('活动id'),
			'nick_name'    => $this->string(255)->comment('昵称'),
			'phone'        => $this->char(32)->comment('手机号'),
			'avatar'       => $this->char(255)->comment('头像'),
			'award_name'   => $this->char(255)->unsigned()->comment('奖品名称'),
			'award_name'   => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('状态 0 未核销 1 已核销'),
			'receive_time' => $this->timestamp()->comment('领取时间'),
			'create_time'  => $this->timestamp()->comment('创建时间')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'中奖纪录表\'');

		$this->addForeignKey('KEY_AWARD_ID', '{{%awards_records}}', 'award_id', '{{%awards_activity}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_014204_add_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_014204_add_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
