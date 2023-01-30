<?php

	use yii\db\Migration;

	/**
	 * Class m201104_024650_change_columns
	 */
	class m201104_024650_change_columns extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_corp}}', 'corp_name', $this->string(255)->comment('授权方企业名称，即企业简称')->after('corpid'));
			$this->alterColumn('{{%work_corp}}', 'corp_full_name', $this->string(255)->comment('授权方企业的主体名称(仅认证或验证过的企业有)，即企业全称。')->after('corp_agent_max'));

			$this->alterColumn('{{%work_external_contact}}', 'name', $this->text()->comment('外部联系人的姓名或别名')->after('external_userid'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201104_024650_change_columns cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201104_024650_change_columns cannot be reverted.\n";

			return false;
		}
		*/
	}
