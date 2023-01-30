<?php

use yii\db\Migration;

/**
 * Class m200327_111550_add_table_awards_join_detail
 */
class m200327_111550_add_table_awards_join_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%awards_join_detail}}', [
			'id'             => $this->primaryKey(11)->unsigned(),
			'awards_join_id' => $this->integer(11)->unsigned()->comment('当前参与者'),
			'external_id'    => $this->integer(11)->unsigned()->comment('外部联系人id'),
			'create_time'    => $this->timestamp()->comment('参与时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'抽奖参与表\'');
		$this->addForeignKey('KEY_AWARDS_JOIN_DETAIL_AWARD_ID', '{{%awards_join_detail}}', 'awards_join_id', '{{%awards_join}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_111550_add_table_awards_join_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_111550_add_table_awards_join_detail cannot be reverted.\n";

        return false;
    }
    */
}
