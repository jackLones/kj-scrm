<?php

	use yii\db\Migration;

	/**
	 * Class m200512_101259_add_table_template_push_info
	 */
	class m200512_101259_add_table_template_push_info extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%template_push_info}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'template_id'  => $this->integer(11)->unsigned()->comment('模板消息群发推送表ID'),
				'fans_id'      => $this->integer(11)->unsigned()->comment('粉丝ID'),
				'message_id'   => $this->char(64)->comment('微信消息ID'),
				'status'       => $this->tinyInteger(2)->unsigned()->comment('发送状态：0：未发送，1：发送成功；2：发送失败；3：发送中'),
				'queue_id'     => $this->integer(11)->unsigned()->comment('发送的队列ID'),
				'errcode'      => $this->string(255)->comment('错误code'),
				'errmsg'       => $this->string(255)->comment('错误信息'),
				'success_time' => $this->timestamp()->comment('成功时间'),
				'send_time'    => $this->timestamp()->comment('发送时间'),
				'create_time'  => $this->timestamp()->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'模板消息发送明细表\'');

			$this->createIndex('KEY_TEMPLATE_push_INFO_MESSAGEID', '{{%template_push_info}}', 'message_id');
			$this->createIndex('KEY_TEMPLATE_push_INFO_QUEUEID', '{{%template_push_info}}', 'queue_id');

			$this->addForeignKey('KEY_TEMPLATE_push_INFO_TEMPLATEID', '{{%template_push_info}}', 'template_id', '{{%template_push_msg}}', 'id');
			$this->addForeignKey('KEY_TEMPLATE_push_INFO_FANSID', '{{%template_push_info}}', 'fans_id', '{{%fans}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200512_101259_add_table_template_push_info cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200512_101259_add_table_template_push_info cannot be reverted.\n";

			return false;
		}
		*/
	}
