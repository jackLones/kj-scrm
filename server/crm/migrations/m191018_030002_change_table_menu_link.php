<?php

	use yii\db\Migration;

	/**
	 * Class m191018_030002_change_table_menu_link
	 */
	class m191018_030002_change_table_menu_link extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->update('{{%menu}}', ['key' => 'scene', 'link' => 'scene/list'], ['id' => 8]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191018_030002_change_table_menu_link cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191018_030002_change_table_menu_link cannot be reverted.\n";

			return false;
		}
		*/
	}
