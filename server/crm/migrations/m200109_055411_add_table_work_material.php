<?php

	use yii\db\Migration;

	/**
	 * Class m200109_055411_add_table_work_material
	 */
	class m200109_055411_add_table_work_material extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_material}}', [
				'id'             => $this->primaryKey(11)->unsigned(),
				'corp_id'        => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'media_id'       => $this->string(128)->comment('新增素材的media_id'),
				'expire'         => $this->char(16)->defaultValue('')->comment('临时素材失效时间'),
				'type'           => $this->tinyInteger(1)->comment('素材有效期类型：0、临时素材；1、永久素材'),
				'material_type'  => $this->tinyInteger(1)->comment('素材类型：1、图文（articles）；2、图片（image）；3、语音（voice）；4、视频（video）；5、文件（file)、6：文本（text）、7：小程序（miniprogram）'),
				'content'        => $this->text()->comment('对于文本类型，content是文本内容，对于图文类型，content是图文描述，，对于小程序类型，content是图片的pic_media_id'),
				'file_name'      => $this->string(128)->defaultValue('')->comment('素材名称或者标题'),
				'media_width'    => $this->char(8)->defaultValue('')->comment('素材宽度'),
				'media_height'   => $this->char(8)->defaultValue('')->comment('素材高度'),
				'media_duration' => $this->char(8)->defaultValue('')->comment('素材时长秒'),
				'file_length'    => $this->integer(11)->defaultValue('0')->comment('素材大小'),
				'content_type'   => $this->char(16)->defaultValue('')->comment('素材类型'),
				'appId'          => $this->string(32)->defaultValue('')->comment('小程序appid'),
				'appPath'        => $this->string(64)->defaultValue('')->comment('小程序page路径'),
				'local_path'     => $this->text()->comment('素材本地地址'),
				'yun_url'        => $this->text()->comment('素材云端地址'),
				'wx_url'         => $this->text()->comment('素材微信地址'),
				'jump_url'       => $this->text()->comment('图文的跳转地址'),
				'created_at'     => $this->integer(11)->comment('媒体文件上传时间戳'),
				'status'         => $this->tinyInteger(1)->defaultValue(1)->comment('1可用 0不可用'),
				'update_time'    => $this->timestamp()->comment('修改时间'),
				'create_time'    => $this->timestamp()->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信素材表\'');

			$this->addForeignKey('KEY_WORK_MATERIAL_CORPID', '{{%work_material}}', 'corp_id', '{{%work_corp}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200109_055411_add_table_work_material cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200109_055411_add_table_work_material cannot be reverted.\n";

			return false;
		}
		*/
	}
