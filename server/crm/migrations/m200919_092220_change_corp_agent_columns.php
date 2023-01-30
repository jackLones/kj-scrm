<?php

	use yii\db\Migration;

	/**
	 * Class m200919_092220_change_corp_agent_columns
	 */
	class m200919_092220_change_corp_agent_columns extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_corp_agent}}', 'allow_party', $this->text()->comment('应用可见范围（部门）')->after('level'));
			$this->alterColumn('{{%work_corp_agent}}', 'allow_user', $this->text()->comment('应用可见范围（成员）')->after('allow_party'));
			$this->alterColumn('{{%work_corp_agent}}', 'allow_tag', $this->text()->comment('应用可见范围（标签）')->after('allow_user'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_party', $this->text()->comment('额外通讯录（部门）')->after('allow_tag'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_user', $this->text()->comment('额外通讯录（成员）')->after('extra_party'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_tag', $this->text()->comment('额外通讯录（标签）')->after('extra_user'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->alterColumn('{{%work_corp_agent}}', 'allow_party', $this->string(255)->comment('应用可见范围（部门）')->after('level'));
			$this->alterColumn('{{%work_corp_agent}}', 'allow_user', $this->string(255)->comment('应用可见范围（成员）')->after('allow_party'));
			$this->alterColumn('{{%work_corp_agent}}', 'allow_tag', $this->string(255)->comment('应用可见范围（标签）')->after('allow_user'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_party', $this->string(255)->comment('额外通讯录（部门）')->after('allow_tag'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_user', $this->string(255)->comment('额外通讯录（成员）')->after('extra_party'));
			$this->alterColumn('{{%work_corp_agent}}', 'extra_tag', $this->string(255)->comment('额外通讯录（标签）')->after('extra_user'));
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200919_092220_change_corp_agent_columns cannot be reverted.\n";

			return false;
		}
		*/
	}
