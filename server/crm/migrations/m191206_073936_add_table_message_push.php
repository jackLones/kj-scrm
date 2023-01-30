<?php

	use yii\db\Migration;

	/**
	 * Class m191206_073936_add_table_message_push
	 */
	class m191206_073936_add_table_message_push extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%message_push}}", [
				'id'          => $this->primaryKey(11)->unsigned(),
				'uid'         => $this->integer(11)->unsigned()->comment("用户id"),
				'title'       => $this->string(64)->comment('消息名称'),
				'sign_id'     => $this->integer(11)->unsigned()->comment('短信签名id'),
				'type_id'     => $this->integer(11)->unsigned()->comment('短信类型id'),
				'content'     => $this->string(300)->comment('发送内容'),
				'send_type'   => $this->tinyInteger(1)->comment('发送对象类型：1、选择已有，2、excel导入，3、手动填写'),
				'send_data'   => $this->text()->comment('发送对象数据'),
				'push_type'   => $this->tinyInteger(1)->defaultValue(1)->comment('群发时间设置：1立即发送、2指定时间'),
				'update_time' => $this->timestamp()->comment('修改时间'),
				'push_time'   => $this->timestamp()->comment('发送时间'),
				'status'      => $this->tinyInteger(1)->defaultValue(0)->comment('状态：0未发送、1已发送、2发送失败、3发送中'),
				'target_num'  => $this->integer(1)->defaultValue(0)->comment('预计发送人数'),
				'arrival_num' => $this->integer(1)->defaultValue(0)->comment('实际发送人数'),
				'queue_id'    => $this->integer(1)->defaultValue(0)->comment('队列id'),
				'is_del'      => $this->tinyInteger(1)->defaultValue(0)->comment('状态：0未删除、1已删除'),
				'create_time' => $this->timestamp()->comment('发送时间'),
				'error_code'  => $this->integer(11)->defaultValue(0)->comment('错误码'),
				'error_msg'   => $this->string(128)->defaultValue('')->comment('错误信息')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信群发表\'');
			$this->addForeignKey('KEY_MESSAGE_PUSH_UID', '{{%message_push}}', 'uid', '{{%user}}', 'uid');
			$this->addForeignKey('KEY_MESSAGE_PUSH_SIGNID', '{{%message_push}}', 'sign_id', '{{%message_sign}}', 'id');
			$this->addForeignKey('KEY_MESSAGE_PUSH_TYPEID', '{{%message_push}}', 'type_id', '{{%message_type}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191206_073936_add_table_message_push cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191206_073936_add_table_message_push cannot be reverted.\n";

			return false;
		}
		*/
	}
