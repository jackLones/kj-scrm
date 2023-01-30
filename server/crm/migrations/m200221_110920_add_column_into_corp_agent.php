<?php

	use yii\db\Migration;

	/**
	 * Class m200221_110920_add_column_into_corp_agent
	 */
	class m200221_110920_add_column_into_corp_agent extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_agent}}', 'description', 'text NULL COMMENT \'企业应用详情\' AFTER `square_logo_url`');
			$this->addColumn('{{%work_corp_agent}}', 'close', 'tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT \'企业应用是否被停用\' AFTER `extra_tag`');
			$this->addColumn('{{%work_corp_agent}}', 'redirect_domain', 'varchar(255) NULL COMMENT \'企业应用可信域名\' AFTER `close`');
			$this->addColumn('{{%work_corp_agent}}', 'report_location_flag', 'tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT \'企业应用是否打开地理位置上报 0：不上报；1：进入会话上报；\' AFTER `redirect_domain`');
			$this->addColumn('{{%work_corp_agent}}', 'isreportenter', 'tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT \'是否上报用户进入应用事件。0：不接收；1：接收\' AFTER `report_location_flag`');
			$this->addColumn('{{%work_corp_agent}}', 'home_url', 'varchar(255) NULL COMMENT \'应用主页url\' AFTER `isreportenter`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200221_110920_add_column_into_corp_agent cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200221_110920_add_column_into_corp_agent cannot be reverted.\n";

			return false;
		}
		*/
	}
