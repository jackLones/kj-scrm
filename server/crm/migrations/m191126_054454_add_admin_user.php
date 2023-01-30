<?php

	use yii\db\Migration;

	/**
	 * Class m191126_054454_add_admin_user
	 */
	class m191126_054454_add_admin_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%admin_user}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'account'      => $this->char(64)->comment('账户名'),
				'password'     => $this->string(250)->comment('加密后的密码'),
				'salt'         => $this->char(6)->comment('加密校验码'),
				'access_token' => $this->string(255)->comment('对接验证字符串'),
				'status'       => $this->tinyInteger(1)->comment('是否启用，1：启用、0：不启用'),
				'update_time'  => $this->timestamp()->comment('修改时间'),
				'create_time'  => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'总后台用户表\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191126_054454_add_admin_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191126_054454_add_admin_user cannot be reverted.\n";

			return false;
		}
		*/
	}
