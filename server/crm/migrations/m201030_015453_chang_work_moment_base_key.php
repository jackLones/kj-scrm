<?php

	use yii\db\Migration;

	/**
	 * Class m201030_015453_chang_work_moment_base_key
	 */
	class m201030_015453_chang_work_moment_base_key extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex("KEY_MOMENT_BASE_PROVINCE", "{{%work_moments_base}}", "province");
			$this->createIndex("KEY_MOMENT_BASE_CITY", "{{%work_moments_base}}", "city");
			$this->createIndex("KEY_MOMENT_BASE_IS_DEL", "{{%work_moments_base}}", "is_del");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201030_015453_chang_work_moment_base_key cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201030_015453_chang_work_moment_base_key cannot be reverted.\n";

			return false;
		}
		*/
	}
