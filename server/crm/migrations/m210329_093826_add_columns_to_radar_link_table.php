<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%radar_link}}`.
	 */
	class m210329_093826_add_columns_to_radar_link_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%radar_link}}', 'content', $this->text()->comment('内容'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropColumn('{{%radar_link}}', 'content');
		}
	}
