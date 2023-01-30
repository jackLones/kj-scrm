<?php

	use yii\db\Migration;

	/**
	 * Class m191126_093355_change_table_fans_material_column
	 */
	class m191126_093355_change_table_fans_material_column extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->renameColumn('{{%fans_msg_material}}', 'image_width', 'media_width');
			$this->renameColumn('{{%fans_msg_material}}', 'image_height', 'media_height');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191126_093355_change_table_fans_material_column cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191126_093355_change_table_fans_material_column cannot be reverted.\n";

			return false;
		}
		*/
	}
