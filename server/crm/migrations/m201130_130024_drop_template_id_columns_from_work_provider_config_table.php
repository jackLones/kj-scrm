<?php

	use yii\db\Migration;

	/**
	 * Handles dropping columns from table `{{%work_provider_config}}`.
	 */
	class m201130_130024_drop_template_id_columns_from_work_provider_config_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->dropColumn('{{%work_provider_config}}', 'template_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->addColumn('{{%work_provider_config}}', 'template_id', $this->string(128)->comment('推广包ID，最长为128个字节')->after('provider_access_token_expires'));
		}
	}
