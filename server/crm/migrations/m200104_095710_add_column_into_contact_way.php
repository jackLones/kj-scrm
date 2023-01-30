<?php

	use yii\db\Migration;

	/**
	 * Class m200104_095710_add_column_into_contact_way
	 */
	class m200104_095710_add_column_into_contact_way extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_contact_way}}', 'qr_code', 'varchar(255) NULL COMMENT \'联系二维码的URL\' AFTER `is_del`');
			$this->addColumn('{{%work_contact_way}}', 'update_time', 'timestamp(0) NULL ON UPDATE CURRENT_TIMESTAMP(0) COMMENT \'更新时间\' AFTER `qr_code`');
			$this->addColumn('{{%work_contact_way}}', 'create_time', 'timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT \'创建时间\' AFTER `update_time`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_095710_add_column_into_contact_way cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_095710_add_column_into_contact_way cannot be reverted.\n";

			return false;
		}
		*/
	}
