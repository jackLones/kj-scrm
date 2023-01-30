<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_register_code}}`.
	 */
	class m201130_124705_add_some_columns_to_work_register_code_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_register_code}}', 'corpid', $this->string(64)->comment('企业的corpid')->after('register_code_expires'));
			$this->addColumn('{{%work_register_code}}', 'contact_sync_access_token', $this->string(255)->comment('通讯录api接口调用凭证，有全部通讯录读写权限。')->after('corpid'));
			$this->addColumn('{{%work_register_code}}', 'contact_sync_access_token_expires', $this->char(16)->comment('access_token凭证的有效时间')->after('contact_sync_access_token'));
			$this->addColumn('{{%work_register_code}}', 'auth_user_info_user_id', $this->string(255)->comment('授权管理员的userid'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_register_code}}', 'corpid');
			$this->dropColumn('{{%work_register_code}}', 'contact_sync_access_token');
			$this->dropColumn('{{%work_register_code}}', 'contact_sync_access_token_expires');
			$this->dropColumn('{{%work_register_code}}', 'auth_user_info_user_id');
		}
	}
