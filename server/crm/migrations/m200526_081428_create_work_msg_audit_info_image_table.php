<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_image}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_081428_create_work_msg_audit_info_image_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_image}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'sdkfileid'     => $this->text()->comment('媒体资源的id信息'),
				'md5sum'        => $this->char(32)->comment('图片资源的md5值，供进行校验'),
				'filesize'      => $this->integer(32)->unsigned()->comment('图片资源的文件大小'),
				'file_path'     => $this->text()->comment('系统内地址'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'图片类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_image-audit_info_id}}',
				'{{%work_msg_audit_info_image}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_image-audit_info_id}}',
				'{{%work_msg_audit_info_image}}',
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
				'{{%fk-work_msg_audit_info_image-audit_info_id}}',
				'{{%work_msg_audit_info_image}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_image-audit_info_id}}',
				'{{%work_msg_audit_info_image}}'
			);

			$this->dropTable('{{%work_msg_audit_info_image}}');
		}
	}
