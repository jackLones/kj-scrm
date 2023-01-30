<?php

	use yii\db\Migration;

	/**
	 * Class m191128_024953_add_user_package
	 */
	class m191128_024953_add_user_package extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%user_package}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'user_id'     => $this->integer(11)->unsigned()->comment('用户id'),
				'package_id'  => $this->integer(11)->unsigned()->comment('套餐id'),
				'start_time'  => $this->integer(11)->notNull()->defaultValue(0)->comment('开始时间'),
				'end_time'    => $this->integer(11)->notNull()->defaultValue(0)->comment('到期时间'),
				'update_time' => $this->timestamp()->comment('修改时间'),
				'create_time' => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'用户套餐关联表\'');
			$this->addForeignKey('KEY_USER_PACKAGE_USERID', '{{%user_package}}', 'user_id', '{{%user}}', 'uid');
			$this->addForeignKey('KEY_USER_PACKAGE_PACKAGEID', '{{%user_package}}', 'package_id', '{{%package}}', 'id');

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191128_024953_add_user_package cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191128_024953_add_user_package cannot be reverted.\n";

			return false;
		}
		*/
	}
