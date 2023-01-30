<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 * - `{{%work_chat}}`
	 */
	class m200526_062620_create_work_msg_audit_info_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'audit_id'    => $this->integer(11)->unsigned()->comment('会话存档ID'),
				'msgid'       => $this->char(64)->notNull()->comment('消息id，消息的唯一标识，企业可以使用此字段进行消息去重'),
				'action'      => $this->char(32)->notNull()->comment('消息动作，send发送消息；recall撤回消息；switch切换企业日志三种类型'),
				'from_type'   => $this->tinyInteger(1)->unsigned()->comment('发送者身份：1、企业成员；2、外部联系人；3、群机器人'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'from'        => $this->char(64)->notNull()->comment('消息发送方id。同一企业内容为userid，非相同企业为external_userid。消息如果是机器人发出，也为external_userid'),
				'tolist'      => $this->text()->notNull()->comment('消息接收方列表，可能是多个，同一个企业内容为userid，非相同企业为external_userid'),
				'chat_id'     => $this->integer(11)->unsigned()->comment('外部群ID'),
				'roomid'      => $this->char(64)->comment('群聊消息的群id。如果是单聊则为空'),
				'msgtype'     => $this->char(32)->notNull()->comment('消息类型：文本：text； 图片：image；撤回：revoke；同意：agree；不同意：disagree；语音：voice；视频：video；名片：card；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar；混合：mixed'),
				'msgtime'     => $this->char(16)->comment('消息发送时间戳，utc时间，ms单位'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话消息汇总表\'');

			// creates index for column `audit_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-audit_id}}',
				'{{%work_msg_audit_info}}',
				'audit_id'
			);

			// add foreign key for table `{{%work_msg_audit}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-audit_id}}',
				'{{%work_msg_audit_info}}',
				'audit_id',
				'{{%work_msg_audit}}',
				'id',
				'CASCADE'
			);

			// creates index for column `msgid`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-msgid}}',
				'{{%work_msg_audit_info}}',
				'msgid'
			);

			// creates index for column `action`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-action}}',
				'{{%work_msg_audit_info}}',
				'action'
			);

			// creates index for column `from_type`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-from_type}}',
				'{{%work_msg_audit_info}}',
				'from_type'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-user_id}}',
				'{{%work_msg_audit_info}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-user_id}}',
				'{{%work_msg_audit_info}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-external_id}}',
				'{{%work_msg_audit_info}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-external_id}}',
				'{{%work_msg_audit_info}}',
				'external_id',
				'{{%work_external_contact}}',
				'id',
				'CASCADE'
			);

			// creates index for column `from`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-from}}',
				'{{%work_msg_audit_info}}',
				'from'
			);

			// creates index for column `chat_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-chat_id}}',
				'{{%work_msg_audit_info}}',
				'chat_id'
			);

			// add foreign key for table `{{%work_chat}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info-chat_id}}',
				'{{%work_msg_audit_info}}',
				'chat_id',
				'{{%work_chat}}',
				'id',
				'CASCADE'
			);

			// creates index for column `msgtype`
			$this->createIndex(
				'{{%idx-work_msg_audit_info-msgtype}}',
				'{{%work_msg_audit_info}}',
				'msgtype'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-audit_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `audit_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-audit_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `msgid`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-msgid}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `action`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-action}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `from_type`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-from_type}}',
				'{{%work_msg_audit_info}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-user_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-user_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-external_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-external_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `from`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-from}}',
				'{{%work_msg_audit_info}}'
			);

			// drops foreign key for table `{{%work_chat}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info-chat_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `chat_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-chat_id}}',
				'{{%work_msg_audit_info}}'
			);

			// drops index for column `msgtype`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info-msgtype}}',
				'{{%work_msg_audit_info}}'
			);

			$this->dropTable('{{%work_msg_audit_info}}');
		}
	}
