<?php

	use yii\db\Migration;

	/**
	 * Class m200529_032201_add_table_work_chat_way_list
	 */
	class m200529_032201_add_table_work_chat_way_list extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_chat_way_list}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'way_id'      => $this->integer(11)->unsigned()->comment('群聊活码ID'),
				'chat_id'     => $this->integer(11)->unsigned()->comment('群列表id'),
				'limit'       => $this->integer(11)->unsigned()->comment('上限'),
				'total'       => $this->integer(11)->unsigned()->comment('群总共人数'),
				'add_num'     => $this->integer(11)->comment('当前群聊人数'),
				'local_path'  => $this->text()->comment('二维码图片本地地址'),
				'create_time' => $this->timestamp()->comment('创建时间'),
				'status'      => $this->tinyInteger(1)->comment('0：禁用；1：启用'),
				'is_del'      => $this->tinyInteger(1)->comment('0：未删除；1：已删除'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'活码群聊对应表\'');
			$this->addForeignKey('KEY_WORK_CHAT_WAY_LIST_WAY_ID', '{{%work_chat_way_list}}', 'way_id', '{{%work_chat_contact_way}}', 'id');
			$this->addForeignKey('KEY_WORK_CHAT_WAY_LIST_CHAT_ID', '{{%work_chat_way_list}}', 'chat_id', '{{%work_chat}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200529_032201_add_table_work_chat_way_list cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200529_032201_add_table_work_chat_way_list cannot be reverted.\n";

			return false;
		}
		*/
	}
