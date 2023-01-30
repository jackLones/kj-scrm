<?php

	use yii\db\Migration;

	/**
	 * Class m190911_022757_add_table_fans_subscribe_event
	 */
	class m190911_022757_add_table_fans_behavior extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%fans_behavior}}", [
				'id'          => $this->primaryKey(11)->unsigned(),
				'fans_id'     => $this->integer(11)->unsigned()->comment("粉丝ID"),
				'fans_event'  => $this->tinyInteger(1)->unsigned()->comment('粉丝行为，1：关注（subscribe）、2：取消关注（unsubscribe）'),
				'year'        => $this->char(4)->comment('年'),
				'month'       => $this->char(2)->comment('月'),
				'day'         => $this->char(2)->comment('日'),
				'create_time' => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'粉丝关注行为表\'');

			$this->createIndex('KEY_FANS_BEHAVIOR_FANSID', '{{%fans_behavior}}', 'fans_id');
			$this->addForeignKey('KEY_FANS_BEHAVIOR_FANSID', '{{%fans_behavior}}', 'fans_id', '{{%fans}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190911_022757_add_table_fans_subscribe_event cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190911_022757_add_table_fans_subscribe_event cannot be reverted.\n";

			return false;
		}
		*/
	}
