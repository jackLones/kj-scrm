<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_key}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit}}`
	 */
	class m200527_033029_create_work_msg_audit_key_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_key}}', [
				'id'               => $this->primaryKey(11)->unsigned(),
				'audit_id'         => $this->integer(11)->unsigned()->comment('会话存档ID'),
				'key_version'      => $this->integer(11)->unsigned()->comment('加密此条消息使用的公钥版本号。Uint32类型'),
				'private_key'      => $this->text()->comment('私钥内容'),
				'private_key_path' => $this->text()->comment('私钥证书地址'),
				'public_key'       => $this->text()->comment('公钥内容'),
				'publick_key_path' => $this->text()->comment('公钥证书地址'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话存档证书版本表\'');

			// creates index for column `audit_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_key-audit_id}}',
				'{{%work_msg_audit_key}}',
				'audit_id'
			);

			// add foreign key for table `{{%work_msg_audit}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_key-audit_id}}',
				'{{%work_msg_audit_key}}',
				'audit_id',
				'{{%work_msg_audit}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_key-audit_id}}',
				'{{%work_msg_audit_key}}'
			);

			// drops index for column `audit_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_key-audit_id}}',
				'{{%work_msg_audit_key}}'
			);

			$this->dropTable('{{%work_msg_audit_key}}');
		}
	}
