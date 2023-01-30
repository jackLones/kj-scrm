<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_corp}}`.
	 */
	class m200820_082534_add_state_columns_to_work_corp_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp}}', 'state', $this->char(16)->null()->comment('企业微信唯一标识')->after('corp_name'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_corp}}', 'state');
		}
	}
