<?php

	use yii\db\Migration;

	/**
	 * Class m200113_061503_add_table_group
	 */
	class m200113_061503_add_table_group extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%attachment_group}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'uid'         => $this->integer(11)->unsigned()->comment('用户ID'),
				'title'       => $this->string(32)->comment('分组名称'),
				'status'      => $this->tinyInteger(1)->defaultValue(1)->comment('1可用 0不可用'),
				'update_time' => $this->timestamp()->comment('修改时间'),
				'create_time' => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'附件分组表\'');
			$this->addForeignKey('KEY_ATTACHMENT_GROUP_UID', '{{%attachment_group}}', 'uid', '{{%user}}', 'uid');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200113_061503_add_table_group cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200113_061503_add_table_group cannot be reverted.\n";

			return false;
		}
		*/
	}
