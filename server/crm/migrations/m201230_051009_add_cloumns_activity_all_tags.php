<?php

	use yii\db\Migration;

	/**
	 * Class m201230_051009_add_cloumns_activity_all_tags
	 */
	class m201230_051009_add_cloumns_activity_all_tags extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%awards_join}}", "tags", $this->char(255)->comment("活动标签"));
			$this->addColumn("{{%red_pack_join}}", "tags", $this->char(255)->comment("活动标签"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201230_051009_add_cloumns_activity_all_tags cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201230_051009_add_cloumns_activity_all_tags cannot be reverted.\n";

			return false;
		}
		*/
	}
