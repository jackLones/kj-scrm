<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_msg_audit_info_chatrecord_item}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info_text}}`
	 * - `{{%work_msg_audit_info_image}}`
	 * - `{{%work_msg_audit_info_video}}`
	 * - `{{%work_msg_audit_info_location}}`
	 * - `{{%work_msg_audit_info_emotion}}`
	 * - `{{%work_msg_audit_info_file}}`
	 * - `{{%work_msg_audit_info_link}}`
	 * - `{{%work_msg_audit_info_weapp}}`
	 * - `{{%work_msg_audit_info_chatrecord}}`
	 * - `{{%work_msg_audit_info_meeting}}`
	 * - `{{%work_msg_audit_info_docmsg}}`
	 * - `{{%work_msg_audit_info_markdown}}`
	 * - `{{%work_msg_audit_info_news}}`
	 */
	class m200604_070632_add_some_columns_to_work_msg_audit_info_chatrecord_item_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'sort', $this->integer(6)->unsigned()->comment('消息排序'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'text_id', $this->integer(11)->unsigned()->comment('文本消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'image_id', $this->integer(11)->unsigned()->comment('图片消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'video_id', $this->integer(11)->unsigned()->comment('视频消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'location_id', $this->integer(11)->unsigned()->comment('位置消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'emotion_id', $this->integer(11)->unsigned()->comment('表情消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'file_id', $this->integer(11)->unsigned()->comment('文件消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'link_id', $this->integer(11)->unsigned()->comment('链接消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'weapp_id', $this->integer(11)->unsigned()->comment('小程序消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'chatrecord_id', $this->integer(11)->unsigned()->comment('会话记录消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'meeting_id', $this->integer(11)->unsigned()->comment('会议消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'docmsg_id', $this->integer(11)->unsigned()->comment('在线文档消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'markdown_id', $this->integer(11)->unsigned()->comment('MarkDown消息ID'));
			$this->addColumn('{{%work_msg_audit_info_chatrecord_item}}', 'news_id', $this->integer(11)->unsigned()->comment('图文消息ID'));

			// creates index for column `text_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-text_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'text_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_text}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-text_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'text_id',
				'{{%work_msg_audit_info_text}}',
				'id',
				'CASCADE'
			);

			// creates index for column `image_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-image_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'image_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_image}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-image_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'image_id',
				'{{%work_msg_audit_info_image}}',
				'id',
				'CASCADE'
			);

			// creates index for column `video_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-video_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'video_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_video}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-video_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'video_id',
				'{{%work_msg_audit_info_video}}',
				'id',
				'CASCADE'
			);

			// creates index for column `location_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-location_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'location_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_location}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-location_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'location_id',
				'{{%work_msg_audit_info_location}}',
				'id',
				'CASCADE'
			);

			// creates index for column `emotion_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-emotion_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'emotion_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_emotion}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-emotion_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'emotion_id',
				'{{%work_msg_audit_info_emotion}}',
				'id',
				'CASCADE'
			);

			// creates index for column `file_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-file_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'file_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_file}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-file_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'file_id',
				'{{%work_msg_audit_info_file}}',
				'id',
				'CASCADE'
			);

			// creates index for column `link_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-link_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'link_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_link}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-link_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'link_id',
				'{{%work_msg_audit_info_link}}',
				'id',
				'CASCADE'
			);

			// creates index for column `weapp_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-weapp_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'weapp_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_weapp}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-weapp_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'weapp_id',
				'{{%work_msg_audit_info_weapp}}',
				'id',
				'CASCADE'
			);

			// creates index for column `chatrecord_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-chatrecord_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'chatrecord_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-chatrecord_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'chatrecord_id',
				'{{%work_msg_audit_info_chatrecord}}',
				'id',
				'CASCADE'
			);

			// creates index for column `meeting_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-meeting_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'meeting_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_meeting}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-meeting_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'meeting_id',
				'{{%work_msg_audit_info_meeting}}',
				'id',
				'CASCADE'
			);

			// creates index for column `docmsg_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-docmsg_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'docmsg_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_docmsg}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-docmsg_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'docmsg_id',
				'{{%work_msg_audit_info_docmsg}}',
				'id',
				'CASCADE'
			);

			// creates index for column `markdown_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-markdown_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'markdown_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_markdown}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-markdown_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'markdown_id',
				'{{%work_msg_audit_info_markdown}}',
				'id',
				'CASCADE'
			);

			// creates index for column `news_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-news_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'news_id'
			);

			// add foreign key for table `{{%work_msg_audit_info_chatrecord_item}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-news_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}',
				'news_id',
				'{{%work_msg_audit_info_news}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info_text}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-text_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `text_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-text_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_image}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-image_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `image_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-image_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_video}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-video_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `video_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-video_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_location}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-location_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `location_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-location_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_emotion}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-emotion_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `emotion_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-emotion_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_file}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-file_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `file_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-file_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_link}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-link_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `link_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-link_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_weapp}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-weapp_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `weapp_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-weapp_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_chatrecord}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-chatrecord_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `chatrecord_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-chatrecord_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_meeting}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-meeting_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `meeting_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-meeting_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_docmsg}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-docmsg_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `docmsg_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-docmsg_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_markdown}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-markdown_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `markdown_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-markdown_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops foreign key for table `{{%work_msg_audit_info_chatrecord_item}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_chatrecord_item-news_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			// drops index for column `news_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_chatrecord_item-news_id}}',
				'{{%work_msg_audit_info_chatrecord_item}}'
			);

			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'sort');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'text_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'image_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'video_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'location_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'emotion_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'file_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'link_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'weapp_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'chatrecord_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'meeting_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'docmsg_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'markdown_id');
			$this->dropColumn('{{%work_msg_audit_info_chatrecord_item}}', 'news_id');
		}
	}
