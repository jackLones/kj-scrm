<?php

	use yii\db\Migration;

	/**
	 * Class m191016_012322_add_last_time_into_tabel_fans
	 */
	class m191016_012322_add_last_time_into_tabel_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%fans}}', 'last_time', 'char(16) NULL COMMENT \'最后活跃时间\' AFTER `qr_scene_str`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191016_012322_add_last_time_into_tabel_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191016_012322_add_last_time_into_tabel_fans cannot be reverted.\n";

			return false;
		}
		*/
	}
