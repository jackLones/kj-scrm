<?php

	use yii\db\Migration;

	/**
	 * Class m200727_060047_change_table_follow
	 */
	class m200727_060047_change_table_follow extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%follow}}', 'describe', $this->string(50)->defaultValue('')->comment('阶段描述')->after('title'));
			$this->addColumn('{{%follow}}', 'sort', $this->integer(11)->unsigned()->defaultValue(0)->comment('排序')->after('status'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200727_060047_change_table_follow cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200727_060047_change_table_follow cannot be reverted.\n";

			return false;
		}
		*/
	}
