<?php

	use yii\db\Migration;

	/**
	 * Class m200529_031640_add_table_work_chat_contact_way
	 */
	class m200529_031640_add_table_work_chat_contact_way extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_chat_contact_way}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'corp_id'      => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'way_group_id' => $this->integer(11)->unsigned()->comment('渠道活码分组id'),
				'config_id'    => $this->char(64)->comment('联系方式的配置id'),
				'title'        => $this->char(200)->comment('活码名称'),
				'type'         => $this->tinyInteger(1)->comment('联系方式类型,1-单人, 2-多人'),
				'scene'        => $this->tinyInteger(1)->comment('场景，1-在小程序中联系，2-通过二维码联系'),
				'style'        => $this->tinyInteger(2)->comment('在小程序中联系时使用的控件样式，详见附表'),
				'remark'       => $this->char(64)->comment('联系方式的备注信息，用于助记，不超过30个字符'),
				'skip_verify'  => $this->tinyInteger(1)->comment('外部客户添加时是否无需验证，默认为true'),
				'state'        => $this->char(64)->comment('企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'is_del'       => $this->tinyInteger(1)->comment('0：未删除；1：已删除'),
				'qr_code'      => $this->string(255)->comment('联系二维码的URL'),
				'add_num'      => $this->integer(11)->comment('添加人数群聊人数'),
				'tag_ids'      => $this->text()->comment('给客户打的标签'),
				'user_key'     => $this->text()->comment('用户选择的key值'),
				'user'         => $this->text()->comment('用户userID列表'),
				'content'      => $this->text()->comment('渠道活码的欢迎语内容'),
				'local_path'   => $this->text()->comment('二维码图片本地地址'),
				'create_time'  => $this->timestamp()->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群聊活码表\'');

			$this->addForeignKey('KEY_WAY_GROUP_ID', '{{%work_chat_contact_way}}', 'way_group_id', '{{%work_chat_contact_way_group}}', 'uid');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200529_031640_add_table_work_chat_contact_way cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200529_031640_add_table_work_chat_contact_way cannot be reverted.\n";

			return false;
		}
		*/
	}
