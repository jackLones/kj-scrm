<?php

	use yii\db\Migration;

	/**
	 * Class m191205_083927_add_table_message_customer
	 */
	class m191205_083927_add_table_message_customer extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%message_customer}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'uid'         => $this->integer(11)->unsigned()->comment('用户ID'),
				'phone'       => $this->string(32)->comment('手机号'),
				'name'        => $this->string(32)->defaultValue('')->comment('姓名'),
				'nickname'    => $this->string(32)->defaultValue('')->comment('微信昵称'),
				'sex'         => $this->tinyInteger(1)->defaultValue(0)->comment('性别，0：未知、1：男、2：女'),
				'remark'      => $this->text()->comment('备注'),
				'status'      => $this->tinyInteger(1)->defaultValue(1)->comment('状态，0：不可用、1：可用'),
				'update_time' => $this->timestamp()->comment('修改时间'),
				'create_time' => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信客户表\'');

			$this->addForeignKey('KEY_MESSAGE_CUSTOMER_UID', '{{%message_customer}}', 'uid', '{{%user}}', 'uid');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191205_083927_add_table_message_customer cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191205_083927_add_table_message_customer cannot be reverted.\n";

			return false;
		}
		*/
	}
