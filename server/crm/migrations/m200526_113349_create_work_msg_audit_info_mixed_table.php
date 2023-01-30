<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_mixed}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 * - `{{%work_msg_audit_info_text}}`
	 * - `{{%work_msg_audit_info_image}}`
	 * - `{{%work_msg_audit_info_voice}}`
	 * - `{{%work_msg_audit_info_video}}`
	 * - `{{%work_msg_audit_info_location}}`
	 * - `{{%work_msg_audit_info_emotion}}`
	 * - `{{%work_msg_audit_info_file}}`
	 * - `{{%work_msg_audit_info_link}}`
	 * - `{{%work_msg_audit_info_weapp}}`
	 * - `{{%work_msg_audit_info_chatrecord}}`
	 * - `{{%work_msg_audit_info_todo}}`
	 * - `{{%work_msg_audit_info_vote}}`
	 * - `{{%work_msg_audit_info_collect}}`
	 * - `{{%work_msg_audit_info_meeting}}`
	 * - `{{%work_msg_audit_info_docmsg}}`
	 * - `{{%work_msg_audit_info_markdown}}`
	 * - `{{%work_msg_audit_info_news}}`
	 * - `{{%work_msg_audit_info_calendar}}`
	 */
	class m200526_113349_create_work_msg_audit_info_mixed_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_mixed}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'type'          => $this->char(16)->comment('消息类型：文本：text； 图片：image；语音：voice；视频：video；位置：location；表情：emotion；文件：file；链接：link；小程序：weapp；会话记录：chatrecord；待办：todo；投票：vote；填表：collect；红包：redpacket；会议邀请：meeting；在线文档：docmsg；MarkDown：markdown；图文：news；日程：calendar'),
				'sort'          => $this->integer(6)->unsigned()->comment('消息排序'),
				'text_id'       => $this->integer(11)->unsigned()->comment('文本消息ID'),
				'image_id'      => $this->integer(11)->unsigned()->comment('图片消息ID'),
				'voice_id'      => $this->integer(11)->unsigned()->comment('语音消息ID'),
				'video_id'      => $this->integer(11)->unsigned()->comment('视频消息ID'),
				'location_id'   => $this->integer(11)->unsigned()->comment('位置消息ID'),
				'emotion_id'    => $this->integer(11)->unsigned()->comment('表情消息ID'),
				'file_id'       => $this->integer(11)->unsigned()->comment('文件消息ID'),
				'link_id'       => $this->integer(11)->unsigned()->comment('链接消息ID'),
				'weapp_id'      => $this->integer(11)->unsigned()->comment('小程序消息ID'),
				'chatrecord_id' => $this->integer(11)->unsigned()->comment('会话记录消息ID'),
				'todo_id'       => $this->integer(11)->unsigned()->comment('待办消息ID'),
				'vote_id'       => $this->integer(11)->unsigned()->comment('投票消息ID'),
				'collect_id'    => $this->integer(11)->unsigned()->comment('填表消息ID'),
				'meeting_id'    => $this->integer(11)->unsigned()->comment('会议消息ID'),
				'docmsg_id'     => $this->integer(11)->unsigned()->comment('在线文档消息ID'),
				'markdown_id'   => $this->integer(11)->unsigned()->comment('MarkDown消息ID'),
				'news_id'       => $this->integer(11)->unsigned()->comment('图文消息ID'),
				'calendar_id'   => $this->integer(11)->unsigned()->comment('日程消息ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'混合消息类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-audit_info_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-audit_info_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);

			// creates index for column `text_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-text_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'text_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_text}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-text_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'text_id',
				'{{%work_msg_audit_info_text}}',
				'id',
				'CASCADE'
			);

			// creates index for column `image_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-image_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'image_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_image}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-image_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'image_id',
				'{{%work_msg_audit_info_image}}',
				'id',
				'CASCADE'
			);

			// creates index for column `voice_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-voice_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'voice_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_voice}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-voice_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'voice_id',
				'{{%work_msg_audit_info_voice}}',
				'id',
				'CASCADE'
			);

			// creates index for column `video_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-video_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'video_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_video}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-video_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'video_id',
				'{{%work_msg_audit_info_video}}',
				'id',
				'CASCADE'
			);

			// creates index for column `location_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-location_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'location_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_location}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-location_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'location_id',
				'{{%work_msg_audit_info_location}}',
				'id',
				'CASCADE'
			);

			// creates index for column `emotion_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-emotion_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'emotion_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_emotion}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-emotion_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'emotion_id',
				'{{%work_msg_audit_info_emotion}}',
				'id',
				'CASCADE'
			);

			// creates index for column `file_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-file_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'file_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_file}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-file_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'file_id',
				'{{%work_msg_audit_info_file}}',
				'id',
				'CASCADE'
			);

			// creates index for column `link_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-link_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'link_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_link}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-link_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'link_id',
				'{{%work_msg_audit_info_link}}',
				'id',
				'CASCADE'
			);

			// creates index for column `weapp_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-weapp_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'weapp_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_weapp}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-weapp_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'weapp_id',
				'{{%work_msg_audit_info_weapp}}',
				'id',
				'CASCADE'
			);

			// creates index for column `chatrecord_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-chatrecord_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'chatrecord_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-chatrecord_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'chatrecord_id',
				'{{%work_msg_audit_info_chatrecord}}',
				'id',
				'CASCADE'
			);

			// creates index for column `todo_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-todo_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'todo_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_todo}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-todo_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'todo_id',
				'{{%work_msg_audit_info_todo}}',
				'id',
				'CASCADE'
			);

			// creates index for column `vote_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-vote_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'vote_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_vote}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-vote_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'vote_id',
				'{{%work_msg_audit_info_vote}}',
				'id',
				'CASCADE'
			);

			// creates index for column `collect_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-collect_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'collect_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_collect}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-collect_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'collect_id',
				'{{%work_msg_audit_info_collect}}',
				'id',
				'CASCADE'
			);

			// creates index for column `meeting_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-meeting_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'meeting_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_meeting}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-meeting_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'meeting_id',
				'{{%work_msg_audit_info_meeting}}',
				'id',
				'CASCADE'
			);

			// creates index for column `docmsg_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-docmsg_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'docmsg_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_docmsg}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-docmsg_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'docmsg_id',
				'{{%work_msg_audit_info_docmsg}}',
				'id',
				'CASCADE'
			);

			// creates index for column `markdown_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-markdown_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'markdown_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_markdown}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-markdown_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'markdown_id',
				'{{%work_msg_audit_info_markdown}}',
				'id',
				'CASCADE'
			);

			// creates index for column `news_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-news_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'news_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_mixed}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-news_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'news_id',
				'{{%work_msg_audit_info_news}}',
				'id',
				'CASCADE'
			);

			// creates index for column `calendar_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_mixed-calendar_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'calendar_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_calendar}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_mixed-calendar_id}}',
				'{{%work_msg_audit_info_mixed}}',
				'calendar_id',
				'{{%work_msg_audit_info_calendar}}',
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
				'{{%fk-work_msg_audit_info_mixed-audit_info_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-audit_info_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_text}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-text_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `text_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-text_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_image}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-image_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `image_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-image_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_voice}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-voice_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `voice_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-voice_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_video}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-video_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `video_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-video_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_location}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-location_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `location_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-location_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_emotion}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-emotion_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `emotion_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-emotion_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_file}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-file_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `file_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-file_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_link}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-link_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `link_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-link_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_weapp}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-weapp_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `weapp_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-weapp_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-chatrecord_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `chatrecord_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-chatrecord_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_todo}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-todo_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `todo_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-todo_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_vote}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-vote_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `vote_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-vote_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_collect}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-collect_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `collect_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-collect_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_meeting}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-meeting_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `meeting_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-meeting_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_docmsg}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-docmsg_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `docmsg_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-docmsg_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_markdown}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-markdown_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `markdown_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-markdown_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_mixed}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-news_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `news_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-news_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_calendar}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_mixed-calendar_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			// drops index for column `calendar_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_mixed-calendar_id}}',
				'{{%work_msg_audit_info_mixed}}'
			);

			$this->dropTable('{{%work_msg_audit_info_mixed}}');
		}
	}
