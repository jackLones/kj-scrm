<?php

	use yii\db\Migration;

	/**
	 * Class m201005_025911_change_table_work_moment
	 */
	class m201005_025911_change_table_work_moment extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_moments_base}}', 'id', 'int(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST');
			$this->addColumn('{{%work_moments}}', 'base_id', 'int(11) unsigned  COMMENT \'base id\' ');
			$this->addForeignKey('key_work_moments_base_id', '{{%work_moments}}', 'base_id', '{{%work_moments_base}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201005_025911_change_table_work_moment cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201005_025911_change_table_work_moment cannot be reverted.\n";

			return false;
		}
		*/
	}
