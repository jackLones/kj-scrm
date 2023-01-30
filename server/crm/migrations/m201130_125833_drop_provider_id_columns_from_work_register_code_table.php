<?php

	use yii\db\Migration;

	/**
	 * Handles dropping columns from table `{{%work_register_code}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_provider_config}}`
	 */
	class m201130_125833_drop_provider_id_columns_from_work_register_code_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			// drops foreign key for table `{{%work_provider_config}}`
			$this->dropForeignKey(
				'{{%fk-work_register_code-provider_id}}',
				'{{%work_register_code}}'
			);

			// drops index for column `provider_id`
			$this->dropIndex(
				'{{%idx-work_register_code-provider_id}}',
				'{{%work_register_code}}'
			);

			$this->dropColumn('{{%work_register_code}}', 'provider_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->addColumn('{{%work_register_code}}', 'provider_id', $this->integer(11)->unsigned()->comment('服务商ID')->after('id'));

			// creates index for column `provider_id`
			$this->createIndex(
				'{{%idx-work_register_code-provider_id}}',
				'{{%work_register_code}}',
				'provider_id'
			);

			// add foreign key for table `{{%work_provider_config}}`
			$this->addForeignKey(
				'{{%fk-work_register_code-provider_id}}',
				'{{%work_register_code}}',
				'provider_id',
				'{{%work_provider_config}}',
				'id',
				'CASCADE'
			);
		}
	}
