<?php

	use yii\db\Migration;

	/**
	 * Class m200103_093048_add_table_work_user_external_profile
	 */
	class m200103_093048_add_table_work_user_external_profile extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_user_external_profile}}', [
				'id'                 => $this->primaryKey(11)->unsigned(),
				'user_id'            => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_position'  => $this->char(64)->comment('对外职务，如果设置了该值，则以此作为对外展示的职务，否则以position来展示。'),
				'external_corp_name' => $this->char(64)->comment('企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态'),
				'external_attr'      => $this->string(255)->comment('属性列表，目前支持文本、网页、小程序三种类型'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信成员对外属性表\'');

			$this->addForeignKey('KEY_WORK_USER_EXTERNAL_PROFILE_USERID', '{{%work_user_external_profile}}', 'user_id', '{{%work_user}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_093048_add_table_work_user_external_profile cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_093048_add_table_work_user_external_profile cannot be reverted.\n";

			return false;
		}
		*/
	}
