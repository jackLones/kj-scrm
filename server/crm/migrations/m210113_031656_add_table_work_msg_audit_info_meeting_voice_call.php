<?php

	use yii\db\Migration;

	/**
	 * Class m210113_031656_add_table_work_msg_audit_info_meeting_voice_call
	 */
	class m210113_031656_add_table_work_msg_audit_info_meeting_voice_call extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_meeting_voice_call}}', [
				'id'              => $this->primaryKey(11)->unsigned(),
				'audit_info_id'   => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'voiceid'         => $this->string(64)->comment('音频id'),
				'endtime'         => $this->integer(11)->unsigned()->comment('音频结束时间'),
				'sdkfileid'       => $this->text()->comment('媒体资源的id信息'),
				'demofiledata'    => $this->text()->comment('文档分享对象数据'),
				'sharescreendata' => $this->text()->comment('屏幕共享数据'),
				'filename'        => $this->text()->comment('文件名称'),
				'filesize'        => $this->integer(32)->unsigned()->comment('文件大小'),
				'file_path'       => $this->text()->comment('系统内地址'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'音频存档类型会话消息表\'');

			$this->createIndex(
				'{{%idx-work_msg_audit_info_meeting_voice_call-audit_info_id}}',
				'{{%work_msg_audit_info_meeting_voice_call}}',
				'audit_info_id'
			);
			$this->createIndex(
				'{{%idx-work_msg_audit_info_meeting_voice_call-voiceid}}',
				'{{%work_msg_audit_info_meeting_voice_call}}',
				'voiceid'
			);
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_meeting_voice_call-audit_info_id}}',
				'{{%work_msg_audit_info_meeting_voice_call}}',
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
			echo "m210113_031656_add_table_work_msg_audit_info_meeting_voice_call cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210113_031656_add_table_work_msg_audit_info_meeting_voice_call cannot be reverted.\n";

			return false;
		}
		*/
	}
