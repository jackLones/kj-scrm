<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_voice}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_082251_create_work_msg_audit_info_voice_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_voice}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'voice_size'    => $this->integer(32)->unsigned()->comment('语音消息大小'),
				'play_length'   => $this->integer(32)->unsigned()->comment('播放长度'),
				'sdkfileid'     => $this->text()->comment('媒体资源的id信息'),
				'md5sum'        => $this->char(32)->comment('资源的md5值，供进行校验'),
				'voice_path'    => $this->text()->comment('系统内地址'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'语音类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_voice-audit_info_id}}',
				'{{%work_msg_audit_info_voice}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_voice-audit_info_id}}',
				'{{%work_msg_audit_info_voice}}',
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
				'{{%fk-work_msg_audit_info_voice-audit_info_id}}',
				'{{%work_msg_audit_info_voice}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_voice-audit_info_id}}',
				'{{%work_msg_audit_info_voice}}'
			);

			$this->dropTable('{{%work_msg_audit_info_voice}}');
		}
	}
