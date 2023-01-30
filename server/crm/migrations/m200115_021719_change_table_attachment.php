<?php

	use yii\db\Migration;

	/**
	 * Class m200115_021719_change_table_attachment
	 */
	class m200115_021719_change_table_attachment extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%attachment}}', 'material_id', 'int(11) unsigned COMMENT \'素材库id，目前只关联图文\'');
			$this->addForeignKey('KEY_ATTACHMENT_MATERIALID', '{{%attachment}}', 'material_id', '{{%material}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200115_021719_change_table_attachment cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200115_021719_change_table_attachment cannot be reverted.\n";

			return false;
		}
		*/
	}
