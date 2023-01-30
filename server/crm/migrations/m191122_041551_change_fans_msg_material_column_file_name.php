<?php

	use yii\db\Migration;

	/**
	 * Class m191122_041551_change_fans_msg_material_column_file_name
	 */
	class m191122_041551_change_fans_msg_material_column_file_name extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%fans_msg_material}}', 'file_name', 'char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'素材名称\' AFTER `material_type`');
			$this->alterColumn('{{%fans_msg_material}}', 'file_length', 'char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'素材大小\' AFTER `image_height`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191122_041551_change_fans_msg_material_column_file_name cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191122_041551_change_fans_msg_material_column_file_name cannot be reverted.\n";

			return false;
		}
		*/
	}
