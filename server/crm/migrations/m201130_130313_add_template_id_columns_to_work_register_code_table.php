<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_register_code}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%template}}`
	 */
	class m201130_130313_add_template_id_columns_to_work_register_code_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_register_code}}', 'template_id', $this->integer(11)->unsigned()->comment('推广包ID')->after('id'));

			// creates index for column `template_id`
			$this->createIndex(
				'{{%idx-work_register_code-template_id}}',
				'{{%work_register_code}}',
				'template_id'
			);

			// add foreign key for table `{{%template}}`
			$this->addForeignKey(
				'{{%fk-work_register_code-template_id}}',
				'{{%work_register_code}}',
				'template_id',
				'{{%work_provider_template}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%template}}`
			$this->dropForeignKey(
				'{{%fk-work_register_code-template_id}}',
				'{{%work_register_code}}'
			);

			// drops index for column `template_id`
			$this->dropIndex(
				'{{%idx-work_register_code-template_id}}',
				'{{%work_register_code}}'
			);

			$this->dropColumn('{{%work_register_code}}', 'template_id');
		}
	}
