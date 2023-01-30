<?php

	use yii\db\Migration;

	/**
	 * Class m210205_050307_add_coloumns_to_work_contact_way_table
	 */
	class m210205_050307_add_columns_to_work_contact_way_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn("{{%work_contact_way}}", "package_del", $this->tinyInteger(1)->defaultValue(0)->comment("0正常1超出套餐删除"));
			$this->addColumn("{{%work_contact_way}}", "is_new", $this->tinyInteger(1)->defaultValue(1)->comment("是否新创建"));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210205_050307_add_coloumns_to_work_contact_way_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210205_050307_add_coloumns_to_work_contact_way_table cannot be reverted.\n";

			return false;
		}
		*/
	}
