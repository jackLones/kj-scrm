<?php

	use yii\db\Migration;

	/**
	 * Class m191208_080107_add_table_message_push_detail
	 */
	class m191208_080107_add_table_message_push_detail extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable("{{%message_push_detail}}", [
				'id'          => $this->primaryKey(11)->unsigned(),
				'uid'         => $this->integer(11)->unsigned()->comment("用户id"),
				'message_id'  => $this->integer(11)->unsigned()->comment("短信群发id"),
				'title'       => $this->string(64)->comment('消息名称'),
				'phone'       => $this->string(32)->comment('手机号码'),
				'sign_name'   => $this->string(64)->comment('短信签名'),
				'type_name'   => $this->string(64)->comment('短信类型'),
				'content'     => $this->string(350)->comment('发送内容'),
				'status'      => $this->tinyInteger(1)->defaultValue(0)->comment('状态：0未发送、1已发送、2发送失败、3发送中'),
				'push_time' => $this->dateTime()->comment('发送时间'),
				'error_code'  => $this->integer(11)->defaultValue(0)->comment('错误码'),
				'error_msg'   => $this->string(128)->defaultValue('')->comment('错误信息')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信群发表\'');
			$this->addForeignKey('KEY_MESSAGE_PUSH_DETAIL_UID', '{{%message_push_detail}}', 'uid', '{{%user}}', 'uid');
			$this->addForeignKey('KEY_MESSAGE_PUSH_DETAIL_MESSAGEID', '{{%message_push_detail}}', 'message_id', '{{%message_push}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191208_080107_add_table_message_push_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191208_080107_add_table_message_push_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
