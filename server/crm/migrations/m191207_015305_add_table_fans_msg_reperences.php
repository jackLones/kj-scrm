<?php

	use yii\db\Migration;

	/**
	 * Class m191207_015305_add_table_fans_msg_reperences
	 */
	class m191207_015305_add_table_fans_msg_reperences extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addForeignKey('KEY_FANS_MSG_MATERIALID', '{{%fans_msg}}', 'material_id', '{{%material}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropForeignKey('KEY_FANS_MSG_MATERIALID', '{{%fans_msg}}');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191207_015305_add_table_fans_msg_reperences cannot be reverted.\n";

			return false;
		}
		*/
	}
