<?php

	use yii\db\Migration;

	/**
	 * Class m200329_102817_add_index_into_material_pull_time
	 */
	class m200329_102817_add_index_into_material_pull_time extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('MATERIAL_PULL_TIME_MATERIALTYPE', '{{%material_pull_time}}', 'material_type');
			$this->createIndex('MATERIAL_PULL_TIME_AUTHORID_MATERIALTYPE_PULLTIME', '{{%material_pull_time}}', ['author_id', 'material_type', 'pull_time']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200329_102817_add_index_into_material_pull_time cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200329_102817_add_index_into_material_pull_time cannot be reverted.\n";

			return false;
		}
		*/
	}
