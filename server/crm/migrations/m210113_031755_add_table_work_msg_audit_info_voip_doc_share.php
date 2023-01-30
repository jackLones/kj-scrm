<?php

	use yii\db\Migration;

	/**
	 * Class m210113_031755_add_table_work_msg_audit_info_voip_doc_share
	 */
	class m210113_031755_add_table_work_msg_audit_info_voip_doc_share extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_voip_doc_share}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'voipid'        => $this->string(64)->comment('音频id'),
				'filename'      => $this->text()->comment('文件名称'),
				'md5sum'        => $this->char(32)->comment('资源的md5值，供进行校验'),
				'filesize'      => $this->integer(32)->unsigned()->comment('语音消息大小'),
				'file_path'     => $this->text()->comment('系统内地址'),
				'sdkfileid'     => $this->text()->comment('媒体资源的id信息'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'音频共享文档类型会话消息表\'');

			$this->createIndex(
				'{{%idx-work_msg_audit_info_voip_doc_share-audit_info_id}}',
				'{{%work_msg_audit_info_voip_doc_share}}',
				'audit_info_id'
			);
			$this->createIndex(
				'{{%idx-work_msg_audit_info_voip_doc_share-voipid}}',
				'{{%work_msg_audit_info_voip_doc_share}}',
				'voipid'
			);
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_voip_doc_share-audit_info_id}}',
				'{{%work_msg_audit_info_voip_doc_share}}',
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
			echo "m210113_031755_add_table_work_msg_audit_info_voip_doc_share cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210113_031755_add_table_work_msg_audit_info_voip_doc_share cannot be reverted.\n";

			return false;
		}
		*/
	}
