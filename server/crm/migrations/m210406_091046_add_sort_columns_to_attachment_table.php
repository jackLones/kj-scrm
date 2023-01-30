<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%attachment}}`.
	 */
	class m210406_091046_add_sort_columns_to_attachment_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%attachment}}', 'sort', $this->integer(11)->unsigned()->comment('排序')->after('status')->notNull());
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%attachment}}', 'sort');
		}
	}
