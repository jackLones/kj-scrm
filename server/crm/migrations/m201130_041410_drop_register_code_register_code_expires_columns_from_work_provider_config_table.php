<?php

	use yii\db\Migration;

	/**
	 * Handles dropping columns from table `{{%work_provider_config}}`.
	 */
	class m201130_041410_drop_register_code_register_code_expires_columns_from_work_provider_config_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropColumn('{{%work_provider_config}}', 'register_code');
			$this->dropColumn('{{%work_provider_config}}', 'register_code_expires');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->addColumn('{{%work_provider_config}}', 'register_code', $this->string(255)->comment('注册码，只能消费一次。在访问注册链接时消费。')->after('provider_access_token_expires'));
			$this->addColumn('{{%work_provider_config}}', 'register_code_expires', $this->char(16)->comment('register_code有效期，生成链接需要在有效期内点击跳转')->after('register_code'));
		}
	}
