<?php

	use yii\db\Migration;

	/**
	 * Class m191011_101043_change_text_collation
	 */
	class m191011_101043_change_text_collation extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%fans_msg}}', 'content', 'text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT \'消息内容\' AFTER `to`');
			$this->alterColumn('{{%wx_msg}}', 'msg_type_value', 'text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT \'消息类别\' AFTER `msg_type`');
			$this->alterColumn('{{%wx_msg}}', 'data', 'longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT \'事件解密后数据\' AFTER `msg_type_value`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191011_101043_change_text_collation cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191011_101043_change_text_collation cannot be reverted.\n";

			return false;
		}
		*/
	}
