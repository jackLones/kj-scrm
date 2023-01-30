<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_vote}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_092510_create_work_msg_audit_info_vote_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_vote}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'votetitle'     => $this->char(64)->comment('投票主题'),
				'voteitem'      => $this->text()->comment('投票选项，可能多个内容'),
				'votetype'      => $this->integer(6)->unsigned()->comment('投票类型.101发起投票、102参与投票'),
				'voteid'        => $this->char(64)->comment('投票id，方便将参与投票消息与发起投票消息进行前后对照'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'投票类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_vote-audit_info_id}}',
				'{{%work_msg_audit_info_vote}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_vote-audit_info_id}}',
				'{{%work_msg_audit_info_vote}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `votetype`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_vote-votetype}}',
				'{{%work_msg_audit_info_vote}}',
				'votetype'
			);

			// creates index for column `voteid`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_vote-voteid}}',
				'{{%work_msg_audit_info_vote}}',
				'voteid'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_vote-audit_info_id}}',
				'{{%work_msg_audit_info_vote}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_vote-audit_info_id}}',
				'{{%work_msg_audit_info_vote}}'
			);

			// drops index for column `votetype`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_vote-votetype}}',
				'{{%work_msg_audit_info_vote}}'
			);

			// drops index for column `voteid`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_vote-voteid}}',
				'{{%work_msg_audit_info_vote}}'
			);

			$this->dropTable('{{%work_msg_audit_info_vote}}');
		}
	}
