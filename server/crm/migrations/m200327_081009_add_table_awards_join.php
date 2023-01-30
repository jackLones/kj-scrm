<?php

	use yii\db\Migration;

	/**
 * Class m200327_081009_add_table_awards_join
 */
class m200327_081009_add_table_awards_join extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_join}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'award_id'    => $this->integer(11)->unsigned()->comment('抽奖活动id'),
			'openid'      => $this->integer(11)->unsigned()->comment('参与者身份openid'),
			'external_id' => $this->integer(11)->unsigned()->comment('外部联系人id'),
			'config_id'   => $this->string(64)->defaultValue('')->comment('联系方式的配置id'),
			'nick_name'   => $this->string(64)->defaultValue('')->comment('昵称'),
			'avatar'      => $this->string(64)->defaultValue('')->comment('头像'),
			'qr_code'     => $this->string(255)->defaultValue('')->comment('联系二维码的URL'),
			'state'       => $this->string(64)->defaultValue('')->comment('企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
			'num'         => $this->integer(11)->defaultValue(0)->comment('获得的抽奖次数'),
			'last_time'   => $this->timestamp()->comment('最后一次中奖时间'),
			'create_time' => $this->timestamp()->comment('参与时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'抽奖参与表\'');
		$this->addForeignKey('KEY_AWARDS_JOIN_AWARD_ID', '{{%awards_join}}', 'award_id', '{{%fission}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_081009_add_table_awards_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_081009_add_table_awards_join cannot be reverted.\n";

        return false;
    }
    */
}
