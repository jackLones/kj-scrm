<?php

	use yii\db\Migration;

	/**
	 * Class m200329_100711_add_index_into_menu
	 */
	class m200329_100711_add_index_into_menu extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createIndex('KEY_MENU_LEVEL', '{{%menu}}', 'level');
			$this->createIndex('KEY_MENU_STATUS', '{{%menu}}', 'status');
			$this->createIndex('KEY_MENU_COMEFROM', '{{%menu}}', 'comefrom');
			$this->createIndex('KEY_MENU_SORT', '{{%menu}}', 'sort');
			$this->createIndex('KEY_MENU_ID_SORT', '{{%menu}}', ['id', 'sort']);
			$this->createIndex('KEY_MENU_PARENTID_SORT', '{{%menu}}', ['parent_id', 'sort']);
			$this->createIndex('KEY_MENU_LEVEL_STATUS_COMEFROM_SORT', '{{%menu}}', ['level', 'status', 'comefrom', 'sort']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200329_100711_add_index_into_menu cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200329_100711_add_index_into_menu cannot be reverted.\n";

			return false;
		}
		*/
	}
