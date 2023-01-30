<?php

	use yii\db\Migration;

	/**
	 * Class m200103_120145_add_cloumn_into_work_user_department_tag
	 */
	class m200103_120145_add_column_into_work_user_department_tag extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_user}}', 'is_del', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：未删除；1：已删除\' AFTER `qr_code`');
			$this->addColumn('{{%work_department}}', 'is_del', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：未删除；1：已删除\' AFTER `order`');
			$this->addColumn('{{%work_tag}}', 'is_del', 'tinyint(1) NULL DEFAULT 0 COMMENT \'0：未删除；1：已删除\' AFTER `tagname`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_120145_add_cloumn_into_work_user_department_tag cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_120145_add_cloumn_into_work_user_department_tag cannot be reverted.\n";

			return false;
		}
		*/
	}
