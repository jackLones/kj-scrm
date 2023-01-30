<?php

	use yii\db\Migration;

	/**
	 * Class m210319_060022_change_work_follow_msg_coloums
	 */
	class m210319_060022_change_work_follow_msg_coloums extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->execute("ALTER TABLE {{%work_follow_msg}}  MODIFY COLUMN `follow_user` longtext  COMMENT '接收成员' ;");
			$this->execute("ALTER TABLE {{%work_follow_msg}}  MODIFY COLUMN `follow_party` longtext  COMMENT '接收部门';");
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210319_060022_change_work_follow_msg_coloums cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{
	
		}
	
		public function down()
		{
			echo "m210319_060022_change_work_follow_msg_coloums cannot be reverted.\n";
	
			return false;
		}
		*/
	}
