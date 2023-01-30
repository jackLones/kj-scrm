<?php

	use yii\db\Migration;

	/**
	 * Class m200108_024540_add_column_into_external_contact
	 */
	class m200108_024540_add_column_into_external_contact extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_external_contact}}', 'way_id', 'int(11) UNSIGNED NULL COMMENT \'联系我配置ID\' AFTER `corp_id`');

			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_WAYID', '{{%work_external_contact}}', 'way_id', '{{%work_contact_way}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200108_024540_add_column_into_external_contact cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200108_024540_add_column_into_external_contact cannot be reverted.\n";

			return false;
		}
		*/
	}
