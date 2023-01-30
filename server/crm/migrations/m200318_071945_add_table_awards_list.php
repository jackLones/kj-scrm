<?php

use yii\db\Migration;

/**
 * Class m200318_071945_add_table_awards_list
 */
class m200318_071945_add_table_awards_list extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_list}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'award_id'    => $this->integer(11)->unsigned()->comment('活动id'),
			'name'        => $this->string(255)->comment('奖品名称'),
			'num'         => $this->integer(11)->comment('奖品数量'),
			'last_num'    => $this->integer(11)->comment('奖品剩余数量'),
			'percentage'  => $this->integer(11)->comment('中奖率'),
			'logo'        => $this->char(255)->unsigned()->comment('奖品图片'),
			'description' => $this->char(255)->unsigned()->comment('说明'),
			'create_time' => $this->timestamp()->comment('创建时间')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'奖项列表\'');

		$this->addForeignKey('KEY_AWARDS_LIST_AWARD_ID', '{{%awards_list}}', 'award_id', '{{%awards_activity}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_071945_add_table_awards_list cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_071945_add_table_awards_list cannot be reverted.\n";

        return false;
    }
    */
}
