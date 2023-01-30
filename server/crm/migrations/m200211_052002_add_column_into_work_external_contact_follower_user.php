<?php

	use yii\db\Migration;

	/**
	 * Class m200211_052002_add_column_into_work_external_contact_follower_user
	 */
	class m200211_052002_add_column_into_work_external_contact_follower_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_external_contact_follow_user}}', 'way_id', 'int(11) UNSIGNED NULL COMMENT \'联系我配置ID\' AFTER `state`');

			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_WAYID', '{{%work_external_contact_follow_user}}', 'way_id', '{{%work_contact_way}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200211_052002_add_column_into_work_external_contact_follower_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200211_052002_add_column_into_work_external_contact_follower_user cannot be reverted.\n";

			return false;
		}
		*/
	}
