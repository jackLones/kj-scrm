<?php

	use yii\db\Migration;

	/**
	 * Class m200217_034238_add_column_into_corp_angent
	 */
	class m200217_034238_add_column_into_corp_angent extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp_agent}}', 'access_token', 'varchar(255) NULL COMMENT \'应用access_token\' AFTER `secret`');
			$this->addColumn('{{%work_corp_agent}}', 'access_token_expires', 'char(16) NULL COMMENT \'access_token有效期\' AFTER `access_token`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200217_034238_add_column_into_corp_angent cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200217_034238_add_column_into_corp_angent cannot be reverted.\n";

			return false;
		}
		*/
	}
