<?php

	use yii\db\Migration;

	/**
	 * Class m201109_074942_change_work_moment_media_att
	 */
	class m201109_074942_change_work_moment_media_att extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_moment_media}}", "att", $this->integer(11)->unsigned()->after("moment_id")->comment("内容引擎id"));
			$this->addColumn("{{%work_moment_news}}", "att", $this->integer(11)->unsigned()->after("moment_id")->comment("内容引擎id"));
			$this->addForeignKey("WORK_MOMENT_MEDIA_ATT", "{{%work_moment_media}}", "att", "{{%attachment}}", "id");
			$this->addForeignKey("WORK_MOMENT_NEW_ATT", "{{%work_moment_news}}", "att", "{{%attachment}}", "id");

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201109_074942_change_work_moment_media_att cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201109_074942_change_work_moment_media_att cannot be reverted.\n";

			return false;
		}
		*/
	}
