<?php

	use yii\db\Migration;

	/**
	 * Class m200104_035451_add_table_handover_user
	 */
	class m200104_035451_add_table_handover_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_handover_user}}', [
				'id'              => $this->primaryKey(11)->unsigned(),
				'handover_userid' => $this->char(64)->comment('离职成员的userid'),
				'external_userid' => $this->char(64)->comment('外部联系人userid'),
				'dimission_time'  => $this->char(16)->comment('成员离职时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信离职成员表\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_035451_add_table_handover_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_035451_add_table_handover_user cannot be reverted.\n";

			return false;
		}
		*/
	}
