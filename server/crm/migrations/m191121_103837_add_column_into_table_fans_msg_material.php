<?php

	use yii\db\Migration;

	/**
	 * Class m191121_103837_add_column_into_table_fans_msg_material
	 */
	class m191121_103837_add_column_into_table_fans_msg_material extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%fans_msg_material}}', 'image_width', 'char(8) NULL COMMENT \'素材宽度\' AFTER `file_name`');
			$this->addColumn('{{%fans_msg_material}}', 'image_height', 'char(8) NULL COMMENT \'素材高度\' AFTER `image_width`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191121_103837_add_column_into_table_fans_msg_material cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191121_103837_add_column_into_table_fans_msg_material cannot be reverted.\n";

			return false;
		}
		*/
	}
