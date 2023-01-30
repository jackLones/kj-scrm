<?php

	use yii\db\Migration;

	/**
	 * Class m191121_070910_add_table_interact_statistic
	 */
	class m191121_070910_add_table_interact_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%interact_statistic}}", [
				'id'          => $this->primaryKey(11)->unsigned(),
				'name'        => $this->integer(11)->unsigned()->comment("公众号名称"),
				'inter_id'    => $this->integer(11)->unsigned()->comment('智能互动表id'),
				'send_num'    => $this->integer(11)->unsigned()->comment('发送次数'),
				'receive_num' => $this->integer(11)->unsigned()->comment('接收次数'),
				'date_time'   => $this->date()->comment('统计时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'智能互动统计表\'');
			$this->addForeignKey('KEY_INTERACT_STAT_INTERID', '{{%interact_statistic}}', 'inter_id', '{{%interact_reply}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191121_070910_add_table_interact_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191121_070910_add_table_interact_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
