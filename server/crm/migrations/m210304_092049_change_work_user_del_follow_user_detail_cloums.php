<?php

	use yii\db\Migration;

	/**
	 * Class m210304_092049_change_work_user_del_follow_user_detail_cloums
	 */
	class m210304_092049_change_work_user_del_follow_user_detail_cloums extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_user_del_follow_user_detail}}", "del_type", $this->tinyInteger(1)->comment("删除时候状态"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210304_092049_change_work_user_del_follow_user_detail_cloums cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210304_092049_change_work_user_del_follow_user_detail_cloums cannot be reverted.\n";

			return false;
		}
		*/
	}
