<?php

	use yii\db\Migration;

	/**
	 * Class m200104_061601_add_table_work_contact_way
	 */
	class m200104_061601_add_table_work_contact_way extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_contact_way}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'config_id'   => $this->char(64)->comment('联系方式的配置id'),
				'type'        => $this->tinyInteger(1)->unsigned()->comment('联系方式类型,1-单人, 2-多人'),
				'scene'       => $this->tinyInteger(1)->unsigned()->comment('场景，1-在小程序中联系，2-通过二维码联系'),
				'style'       => $this->tinyInteger(2)->unsigned()->comment('在小程序中联系时使用的控件样式，详见附表'),
				'remark'      => $this->char(64)->comment('联系方式的备注信息，用于助记，不超过30个字符'),
				'skip_verify' => $this->boolean()->comment('外部客户添加时是否无需验证，默认为true'),
				'state'       => $this->char(64)->comment('企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'is_del'      => $this->tinyInteger(1)->defaultValue(0)->comment('0：未删除；1：已删除'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信联系我表\'');
			$this->createIndex('KEY_WORK_CONTACT_WAY_CONFIGID', '{{%work_contact_way}}', 'config_id');
			$this->addForeignKey('KEY_WORK_CONTACT_WAY_CORPID', '{{%work_contact_way}}', 'corp_id', '{{%work_corp}}', 'id');

			$this->createTable('{{%work_contact_way_user}}', [
				'id'        => $this->primaryKey(11)->unsigned(),
				'config_id' => $this->integer(11)->unsigned()->comment('联系方式的配置id'),
				'user_id'   => $this->integer(11)->unsigned()->comment('成员ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信联系我成员表\'');
			$this->addForeignKey('KEY_WORK_CONTACT_WAY_USER_CONFIGID', '{{%work_contact_way_user}}', 'config_id', '{{%work_contact_way}}', 'id');
			$this->addForeignKey('KEY_WORK_CONTACT_WAY_USER_USERID', '{{%work_contact_way_user}}', 'user_id', '{{%work_user}}', 'id');

			$this->createTable('{{%work_contact_way_department}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'config_id'     => $this->integer(11)->unsigned()->comment('联系方式的配置id'),
				'department_id' => $this->integer(11)->unsigned()->comment('成员ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信联系我部门表\'');
			$this->addForeignKey('KEY_WORK_CONTACT_WAY_DEPARTMENT_CONFIGID', '{{%work_contact_way_department}}', 'config_id', '{{%work_contact_way}}', 'id');
			$this->addForeignKey('KEY_WORK_CONTACT_WAY_DEPARTMENT_DEPARTMENTID', '{{%work_contact_way_department}}', 'department_id', '{{%work_department}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200104_061601_add_table_work_contact_way cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200104_061601_add_table_work_contact_way cannot be reverted.\n";

			return false;
		}
		*/
	}
