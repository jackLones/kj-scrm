<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_emotion}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_084158_create_work_msg_audit_info_emotion_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_emotion}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'type'          => $this->tinyinteger(1)->unsigned()->comment('表情类型，png或者gif.1表示gif 2表示png'),
				'width'         => $this->integer(11)->unsigned()->comment('表情图片宽度'),
				'height'        => $this->integer(11)->unsigned()->comment('表情图片高度'),
				'sdkfileid'     => $this->text()->comment('媒体资源的id信息'),
				'md5sum'        => $this->char(32)->comment('资源的md5值，供进行校验'),
				'imagesize'     => $this->integer(32)->unsigned()->comment('资源的文件大小'),
				'local_path'    => $this->text()->comment('系统内地址'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'表情类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_emotion-audit_info_id}}',
				'{{%work_msg_audit_info_emotion}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_emotion-audit_info_id}}',
				'{{%work_msg_audit_info_emotion}}',
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
				'{{%fk-work_msg_audit_info_emotion-audit_info_id}}',
				'{{%work_msg_audit_info_emotion}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_emotion-audit_info_id}}',
				'{{%work_msg_audit_info_emotion}}'
			);

			$this->dropTable('{{%work_msg_audit_info_emotion}}');
		}
	}
