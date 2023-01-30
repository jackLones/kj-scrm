<?php

	use yii\db\Migration;

	/**
	 * Class m201218_065930_change_work_external_contact_external_profile_table
	 */
	class m201218_065930_change_work_external_contact_external_profile_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_external_contact_external_profile}}', 'external_attr', $this->text()->comment('属性列表，目前支持文本、网页、小程序三种类型'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->alterColumn('{{%work_external_contact_external_profile}}', 'external_attr', $this->string(255)->comment('属性列表，目前支持文本、网页、小程序三种类型'));
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201218_065930_change_work_external_contact_external_profile_table cannot be reverted.\n";

			return false;
		}
		*/
	}
