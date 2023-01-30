<?php

	use yii\db\Migration;

	/**
	 * Class m191119_074040_add_fans_msg_material_table
	 */
	class m191119_074040_add_fans_msg_material_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%fans_msg_material}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'author_id'     => $this->integer(11)->unsigned()->comment('公众号ID'),
				'fans_id'       => $this->integer(11)->unsigned()->comment("粉丝ID"),
				'msg_id'        => $this->integer(11)->unsigned()->comment("粉丝消息ID"),
				'media_id'      => $this->char(64)->notNull()->comment('粉丝发送的media_id'),
				'material_type' => $this->tinyInteger(1)->notNull()->comment('素材类型：2、图片（image）；3、语音（voice）；4、视频（video）；6：音乐素材（music）'),
				'file_name'     => $this->char(16)->comment('素材名称'),
				'file_length'   => $this->char(8)->comment('素材大小'),
				'content_type'  => $this->char(16)->comment('素材类型'),
				'local_path'    => $this->text()->comment('素材本地地址'),
				'yun_url'       => $this->text()->comment('素材云端地址'),
				'wx_url'        => $this->text()->comment('素材微信地址'),
				'create_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'粉丝消息素材表\'');

			$this->createIndex('IDX_FANS_MSG_MATERIAL_AUTHORID', '{{%fans_msg_material}}', 'author_id');
			$this->createIndex('IDX_FANS_MSG_MATERIAL_FANSID', '{{%fans_msg_material}}', 'fans_id');
			$this->createIndex('IDX_FANS_MSG_MATERIAL_MSGID', '{{%fans_msg_material}}', 'msg_id');
			$this->createIndex('IDX_FANS_MSG_MATERIAL_MEDIAID', '{{%fans_msg_material}}', 'media_id');
			$this->createIndex('IDX_FANS_MSG_MATERIAL_MATERIALTYPE', '{{%fans_msg_material}}', 'material_type');

			$this->addForeignKey('KEY_FANS_MSG_MATERIAL_AUTHORID', '{{%fans_msg_material}}', 'author_id', '{{%wx_authorize}}', 'author_id');
			$this->addForeignKey('KEY_FANS_MSG_MATERIAL_FANSID', '{{%fans_msg_material}}', 'fans_id', '{{%fans}}', 'id');
			$this->addForeignKey('KEY_FANS_MSG_MATERIAL_MSGID', '{{%fans_msg_material}}', 'msg_id', '{{%fans_msg}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191119_074040_add_fans_msg_material_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191119_074040_add_fans_msg_material_table cannot be reverted.\n";

			return false;
		}
		*/
	}
