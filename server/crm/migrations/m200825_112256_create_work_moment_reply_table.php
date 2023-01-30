<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_reply}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_moments}}`
	 * - `{{%work_moment_reply}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200825_112256_create_work_moment_reply_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_reply}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'moment_id'   => $this->integer(11)->unsigned()->comment('朋友圈ID'),
				'reply_id'    => $this->integer(11)->unsigned()->comment('回复ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'openid'      => $this->char(64)->comment('外部非联系人openid'),
				'content'     => $this->char(64)->notNull()->comment('回复内容'),
				'status'      => $this->tinyInteger(1)->unsigned()->comment('状态：0、删除；1：正常'),
				'del_time'    => $this->timestamp()->comment('删除时间'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈回复详情表\'');

			// creates index for column `moment_id`
			$this->createIndex(
				'{{%idx-work_moment_reply-moment_id}}',
				'{{%work_moment_reply}}',
				'moment_id'
			);

			// add foreign key for table `{{%work_moments}}`
			$this->addForeignKey(
				'{{%fk-work_moment_reply-moment_id}}',
				'{{%work_moment_reply}}',
				'moment_id',
				'{{%work_moments}}',
				'id',
				'CASCADE'
			);

			// creates index for column `reply_id`
			$this->createIndex(
				'{{%idx-work_moment_reply-reply_id}}',
				'{{%work_moment_reply}}',
				'reply_id'
			);

			// add foreign key for table `{{%work_moment_reply}}`
			$this->addForeignKey(
				'{{%fk-work_moment_reply-reply_id}}',
				'{{%work_moment_reply}}',
				'reply_id',
				'{{%work_moment_reply}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_moment_reply-user_id}}',
				'{{%work_moment_reply}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_moment_reply-user_id}}',
				'{{%work_moment_reply}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_moment_reply-external_id}}',
				'{{%work_moment_reply}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_moment_reply-external_id}}',
				'{{%work_moment_reply}}',
				'external_id',
				'{{%work_external_contact}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_moments}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_reply-moment_id}}',
				'{{%work_moment_reply}}'
			);

			// drops index for column `moment_id`
			$this->dropIndex(
				'{{%idx-work_moment_reply-moment_id}}',
				'{{%work_moment_reply}}'
			);

			// drops foreign key for table `{{%work_moment_reply}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_reply-reply_id}}',
				'{{%work_moment_reply}}'
			);

			// drops index for column `reply_id`
			$this->dropIndex(
				'{{%idx-work_moment_reply-reply_id}}',
				'{{%work_moment_reply}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_reply-user_id}}',
				'{{%work_moment_reply}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_moment_reply-user_id}}',
				'{{%work_moment_reply}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_reply-external_id}}',
				'{{%work_moment_reply}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_moment_reply-external_id}}',
				'{{%work_moment_reply}}'
			);

			$this->dropTable('{{%work_moment_reply}}');
		}
	}
