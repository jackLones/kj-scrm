<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_provider_config}}`.
	 */
	class m201130_040703_add_template_id_columns_to_work_provider_config_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_provider_config}}', 'template_id', $this->string(128)->comment('推广包ID，最长为128个字节')->after('provider_access_token_expires'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%work_provider_config}}', 'template_id');
		}
	}
