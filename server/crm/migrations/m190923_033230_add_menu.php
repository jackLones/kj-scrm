<?php

	use yii\db\Migration;

	/**
	 * Class m190923_033230_add_menu
	 */
	class m190923_033230_add_menu extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->insert('{{%menu}}', [
				'id'        => 17,
				'parent_id' => NULL,
				'title'     => '公众号管理',
				'icon'      => 'project',
				'key'       => 'account',
				'link'      => 'account',
				'level'     => 1,
				'sort'      => 6,
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190923_033230_add_menu cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190923_033230_add_menu cannot be reverted.\n";

			return false;
		}
		*/
	}
