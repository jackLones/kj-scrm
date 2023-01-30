<?php

	use yii\db\Migration;

	/**
	 * Class m200104_024416_add_work_external_user_table
	 */
	class m200104_024416_add_work_external_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_external_contact}}', [
				'id'              => $this->primaryKey(11)->unsigned(),
				'corp_id'         => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'external_userid' => $this->char(64)->comment('外部联系人的userid'),
				'name'            => $this->char(64)->comment('外部联系人的姓名或别名'),
				'position'        => $this->char(64)->comment('外部联系人的职位，如果外部企业或用户选择隐藏职位，则不返回，仅当联系人类型是企业微信用户时有此字段'),
				'avatar'          => $this->string(255)->comment('外部联系人头像，第三方不可获取'),
				'corp_name'       => $this->char(64)->comment('外部联系人所在企业的简称，仅当联系人类型是企业微信用户时有此字段'),
				'corp_full_name'  => $this->char(64)->comment('外部联系人所在企业的主体名称，仅当联系人类型是企业微信用户时有此字段'),
				'type'            => $this->tinyInteger(2)->comment('外部联系人的类型，1表示该外部联系人是微信用户，2表示该外部联系人是企业微信用户'),
				'gender'          => $this->tinyInteger(2)->comment('外部联系人性别 0-未知 1-男性 2-女性'),
				'unionid'         => $this->char(64)->comment('外部联系人在微信开放平台的唯一身份标识（微信unionid），通过此字段企业可将外部联系人与公众号/小程序用户关联起来。仅当联系人类型是微信用户，且企业绑定了微信开发者ID有此字段。查看绑定方法'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信外部联系人表\'');
			$this->createIndex('KEY_WORK_EXTERNAL_CONTACT_EXTERNALUSERID', '{{%work_external_contact}}', 'external_userid');
			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_CORPID', '{{%work_external_contact}}', 'corp_id', '{{%work_corp}}', 'id');

			$this->createTable('{{%work_external_contact_external_profile}}', [
				'id'                 => $this->primaryKey(11)->unsigned(),
				'external_userid'    => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'external_corp_name' => $this->char(64)->comment('企业对外简称，需从已认证的企业简称中选填。可在“我的企业”页中查看企业简称认证状态'),
				'external_attr'      => $this->string(255)->comment('属性列表，目前支持文本、网页、小程序三种类型'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信外部联系人对外属性表\'');
			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_EXTERNAL_PROFILE_EXTERNALUSERID', '{{%work_external_contact_external_profile}}', 'external_userid', '{{%work_external_contact}}', 'id');

			$this->createTable('{{%work_external_contact_follow_user}}', [
				'id'               => $this->primaryKey(11)->unsigned(),
				'external_userid'  => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'user_id'          => $this->integer(11)->unsigned()->comment('成员ID'),
				'userid'           => $this->char(64)->comment('添加了此外部联系人的企业成员userid'),
				'remark'           => $this->char(64)->comment('该成员对此外部联系人的备注'),
				'description'      => $this->char(64)->comment('该成员对此外部联系人的描述'),
				'createtime'       => $this->char(64)->comment('该成员添加此外部联系人的时间'),
				'tags'             => $this->string(255)->comment('该成员添加此外部联系人所打标签的分组名称（标签功能需要企业微信升级到2.7.5及以上版本）'),
				'remark_corp_name' => $this->char(64)->comment('该成员对此客户备注的企业名称'),
				'remark_mobiles'   => $this->string(255)->comment('该成员对此客户备注的手机号码，第三方不可获取'),
				'state'            => $this->string(255)->comment('该成员添加此客户的渠道，由用户通过创建「联系我」方式指定'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信外部联系人添加信息表\'');
			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_EXTERNALUSERID', '{{%work_external_contact_follow_user}}', 'external_userid', '{{%work_external_contact}}', 'id');
			$this->addForeignKey('KEY_WORK_EXTERNAL_CONTACT_FOLLOW_USER_USERID', '{{%work_external_contact_follow_user}}', 'user_id', '{{%work_user}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_024416_add_work_external_user_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_024416_add_work_external_user_table cannot be reverted.\n";

			return false;
		}
		*/
	}
