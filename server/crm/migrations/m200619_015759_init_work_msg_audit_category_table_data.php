<?php

	use yii\db\Migration;

	/**
	 * Class m200619_015759_init_work_msg_audit_category_table_data
	 */
	class m200619_015759_init_work_msg_audit_category_table_data extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->batchInsert('{{%work_msg_audit_category}}', ['category_type', 'category_name'], [
				['text', '文本'],
				['image', '图片'],
				['revoke', '撤回'],
				['agree', '同意'],
				['disagree', '不同意'],
				['voice', '语音'],
				['video', '视频'],
				['card', '名片'],
				['location', '位置'],
				['emotion', 'Emotion 表情'],
				['file', '文件'],
				['link', '链接'],
				['weapp', '小程序'],
				['chatrecord', '聊天记录'],
				['todo', '待办'],
				['vote', '投票'],
				['collect', '填表'],
				['redpacket', '红包'],
				['meeting', '会议'],
				['docmsg', '在线文档'],
				['markdown', 'Markdown'],
				['news', '图文'],
				['calendar', '日程'],
				['mixed', '混合'],
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200619_015759_init_work_msg_audit_category_table_data cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200619_015759_init_work_msg_audit_category_table_data cannot be reverted.\n";

			return false;
		}
		*/
	}
