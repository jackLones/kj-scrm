<?php

	use yii\db\Migration;

	/**
	 * Class m200410_095735_add_table_attachment_statistic
	 */
	class m200410_095735_add_table_attachment_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%attachment_statistic}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'attachment_id' => $this->integer(11)->unsigned()->notNull()->comment('素材ID'),
				'user_id'       => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('成员ID'),
				'external_id'   => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('外部联系人ID'),
				'openid'        => $this->char(64)->defaultValue(NULL)->comment('用户openid'),
				'type'          => $this->tinyInteger(1)->unsigned()->comment('类型：1：搜索、2：发送、3：打开'),
				'search'        => $this->string(255)->unsigned()->comment('搜索内容'),
				'open_time'     => $this->timestamp()->defaultValue(NULL)->comment('打开时间'),
				'leave_time'    => $this->timestamp()->defaultValue(NULL)->comment('离开时间'),
				'create_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'素材统计表\'');

			$this->addForeignKey('KEY_ATTACHMENT_STATISTIC_ATTACHMENTID', '{{%attachment_statistic}}', 'attachment_id', '{{%attachment}}', 'id');
			$this->addForeignKey('KEY_ATTACHMENT_STATISTIC_USERID', '{{%attachment_statistic}}', 'user_id', '{{%work_user}}', 'id');
			$this->addForeignKey('KEY_ATTACHMENT_STATISTIC_EXTERNALID', '{{%attachment_statistic}}', 'external_id', '{{%work_external_contact}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200410_095735_add_table_attachment_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200410_095735_add_table_attachment_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
