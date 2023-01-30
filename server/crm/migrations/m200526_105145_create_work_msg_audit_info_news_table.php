<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_news}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_105145_create_work_msg_audit_info_news_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_news}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'title'         => $this->char(64)->comment('图文消息标题'),
				'description'   => $this->string(255)->comment('图文消息描述'),
				'url'           => $this->text()->comment('图文消息点击跳转地址'),
				'picurl'        => $this->text()->comment('图文消息配图的url'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'图文类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_news-audit_info_id}}',
				'{{%work_msg_audit_info_news}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_news-audit_info_id}}',
				'{{%work_msg_audit_info_news}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_news-audit_info_id}}',
				'{{%work_msg_audit_info_news}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_news-audit_info_id}}',
				'{{%work_msg_audit_info_news}}'
			);

			$this->dropTable('{{%work_msg_audit_info_news}}');
		}
	}
