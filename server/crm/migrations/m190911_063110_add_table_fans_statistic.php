<?php

	use yii\db\Migration;

	/**
	 * Class m190911_063110_add_table_fans_statistic
	 */
	class m190911_063110_add_table_fans_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%fans_statistic}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'author_id'    => $this->integer(11)->unsigned()->comment('公众号ID'),
				'new'          => $this->integer(11)->unsigned()->comment('新增粉丝数'),
				'unsubscribe'  => $this->integer(11)->unsigned()->comment('取关粉丝数'),
				'net_increase' => $this->integer(11)->unsigned()->comment('净增粉丝数'),
				'active'       => $this->integer(11)->unsigned()->comment('活跃粉丝数'),
				'total'        => $this->integer(11)->unsigned()->comment('总粉丝数'),
				'data_time'    => $this->timestamp()->defaultValue(NULL)->comment('数据统计日期'),
				'create_time'  => $this->timestamp()->defaultValue(NULL)->comment('创建日期')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'粉丝日统计\'');

			$this->createIndex('KEY_FANS_STATISTIC_AUTHORID', '{{%fans_statistic}}', 'author_id');

			$this->addForeignKey('KEY_FANS_STATISTIC_AUTHORID', '{{%fans_statistic}}', 'author_id', '{{%wx_authorize}}', 'author_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190911_063110_add_table_fans_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190911_063110_add_table_fans_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
