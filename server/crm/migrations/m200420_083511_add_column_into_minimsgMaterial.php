<?php

	use yii\db\Migration;

	/**
	 * Class m200420_083511_add_column_into_minimsgMaterial
	 */
	class m200420_083511_add_column_into_minimsgMaterial extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%mini_msg_material}}', 'media_id', 'char(64) NOT NULL COMMENT \'用户发送的media_id\' AFTER `msg_id`');

			$this->createIndex('key_mini_msg_material_mediaid', '{{%mini_msg_material}}', 'media_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200420_083511_add_column_into_minimsgMaterial cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200420_083511_add_column_into_minimsgMaterial cannot be reverted.\n";

			return false;
		}
		*/
	}
