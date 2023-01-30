<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%user}}`.
	 */
	class m210202_074713_add_columns_to_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%user}}", "sub_num", $this->integer(11)->defaultValue(0)->comment("允许子账户数量"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
		}
	}
