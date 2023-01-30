<?php

	use yii\db\Migration;

	/**
	 * Class m190923_034100_change_menu_name
	 */
	class m190923_034100_change_menu_name extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->update('{{%menu}}', ['title' => '微信素材'], ['id' => 16]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190923_034100_change_menu_name cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190923_034100_change_menu_name cannot be reverted.\n";

			return false;
		}
		*/
	}
