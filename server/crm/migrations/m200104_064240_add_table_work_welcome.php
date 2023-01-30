<?php

	use yii\db\Migration;

	/**
	 * Class m200104_064240_add_table_work_welcome
	 */
	class m200104_064240_add_table_work_welcome extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_welcome}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'type'          => $this->tinyInteger(2)->unsigned()->comment('1：全体成员；2：成员；3：部门'),
				'corp_id'       => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
				'department_id' => $this->integer(11)->unsigned()->comment('成员ID'),
				'context'       => $this->text()->comment('欢迎语内容'),
				'update_time'   => $this->timestamp()->comment('更新时间'),
				'create_time'   => $this->timestamp()->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信欢迎语表\'');
			$this->addForeignKey('KEY_WORK_WELCOME_CORPID', '{{%work_welcome}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_WELCOME_USERID', '{{%work_welcome}}', 'user_id', '{{%work_contact_way_user}}', 'id');
			$this->addForeignKey('KEY_WORK_WELCOME_DEPARTMENTID', '{{%work_welcome}}', 'department_id', '{{%work_contact_way_department}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_064240_add_table_work_welcome cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_064240_add_table_work_welcome cannot be reverted.\n";

			return false;
		}
		*/
	}
