<?php

	use app\models\Menu;
	use yii\db\Migration;

	/**
	 * Class m210325_015744_change_menu_status
	 */
	class m210325_015744_change_menu_status extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			Menu::updateAll(["status" => 0], ["key" => "redForNew"]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210325_015744_change_menu_status cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210325_015744_change_menu_status cannot be reverted.\n";

			return false;
		}
		*/
	}
