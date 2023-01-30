<?php

	use yii\db\Migration;

	/**
	 * Class m201023_061444_chang_fans_or_follow_user_table
	 */
	class m201023_061444_chang_fans_or_follow_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_external_contact_follow_user}}', 'activity_id', $this->integer(11)->unsigned()->comment('任务宝id')->after('follow_num'));
			$this->update('{{%work_external_contact_follow_user}}', ['activity_id' => NULL]);
			$this->addForeignKey('KEY_WORK_FOLLOW_USER_ACTIVITY_ID', '{{%work_external_contact_follow_user}}', 'activity_id', '{{%work_public_activity}}', 'id');
			$this->alterColumn('{{%fans}}', 'activity_id', $this->integer(11)->unsigned()->comment('任务宝id')->after('external_userid'));
			$this->update('{{%fans}}', ['activity_id' => NULL]);
			$this->addForeignKey('KEY_WECHAT_FANS_ACTIVITY_ID', '{{%fans}}', 'activity_id', '{{%work_public_activity}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201023_061444_chang_fans_or_follow_user_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201023_061444_chang_fans_or_follow_user_table cannot be reverted.\n";

			return false;
		}
		*/
	}
