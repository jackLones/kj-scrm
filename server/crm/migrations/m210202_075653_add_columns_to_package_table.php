<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%package}}`.
	 */
	class m210202_075653_add_columns_to_package_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%package}}", "follow_open", $this->tinyInteger(1)->defaultValue(0)->comment("渠道活码限制开启关闭"));
			$this->addColumn("{{%package}}", "follow_num", $this->integer(11)->defaultValue(0)->comment("单个渠道活码添加上限"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
		}
	}
