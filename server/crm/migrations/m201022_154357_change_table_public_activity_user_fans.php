<?php

	use yii\db\Migration;

	/**
	 * Class m201022_154357_change_table_public_activity_user_fans
	 */
	class m201022_154357_change_table_public_activity_user_fans extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_public_activity_fans_user}}', 'external_userid', $this->integer(11)->unsigned()->comment('外部联系人id')->after('parent_id'));
			$this->addForeignKey('KEY_WORK_PUBLIC_ACTIVITY_USER_FANS_EXT_USERID', '{{%work_public_activity_fans_user}}', 'external_userid', '{{%work_external_contact}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201022_154357_change_table_public_activity_user_fans cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201022_154357_change_table_public_activity_user_fans cannot be reverted.\n";

			return false;
		}
		*/
	}
