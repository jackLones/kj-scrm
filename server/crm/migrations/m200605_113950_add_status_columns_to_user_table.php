<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%user}}`.
	 */
	class m200605_113950_add_status_columns_to_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%user}}', 'status', $this->tinyInteger(1)->unsigned()->after('salt')->defaultValue(1)->comment('账号状态：0、禁用；1、正常'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%user}}', 'status');
		}
	}
