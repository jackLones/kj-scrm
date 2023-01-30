<?php

use yii\db\Migration;

/**
 * Class m200328_031610_add_table_awards_share
 */
class m200328_031610_add_table_awards_share extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_share}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'join_id'     => $this->integer(11)->unsigned()->comment('参与者id'),
			'num'         => $this->integer(11)->unsigned()->comment('获得的抽奖次数'),
			'create_time' => $this->timestamp()->comment('参与时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'抽奖分享记录表\'');
		$this->addForeignKey('KEY_AWARDS_SHARE_JOIN_ID', '{{%awards_share}}', 'join_id', '{{%awards_join}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200328_031610_add_table_awards_share cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200328_031610_add_table_awards_share cannot be reverted.\n";

        return false;
    }
    */
}
