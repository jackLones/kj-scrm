<?php

	use yii\db\Migration;

	/**
	 * Class m191127_094001_add_column_into_table_fans_msg_material
	 */
	class m191127_094001_add_column_into_table_fans_msg_material extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%fans_msg_material}}', 'media_duration', 'char(16) NULL DEFAULT NULL COMMENT \'多媒体素材时长\' AFTER `media_height`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191127_094001_add_column_into_table_fans_msg_material cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191127_094001_add_column_into_table_fans_msg_material cannot be reverted.\n";

			return false;
		}
		*/
	}
