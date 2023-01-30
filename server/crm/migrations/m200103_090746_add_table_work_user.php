<?php

	use yii\db\Migration;

	/**
	 * Class m200103_090746_add_table_work_user
	 */
	class m200103_090746_add_table_work_user extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_user}}', [
				'id'                => $this->primaryKey(11)->unsigned(),
				'corp_id'           => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'userid'            => $this->char(64)->comment('成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节'),
				'name'              => $this->char(64)->comment('成员名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示名字'),
				'department'        => $this->char(64)->comment('成员所属部门id列表，仅返回该应用有查看权限的部门id'),
				'order'             => $this->char(64)->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。值范围是[0, 2^32)'),
				'position'          => $this->char(64)->comment('职务信息；第三方仅通讯录应用可获取'),
				'mobile'            => $this->char(64)->comment('手机号码，第三方仅通讯录应用可获取'),
				'gender'            => $this->char(64)->comment('性别。0表示未定义，1表示男性，2表示女性'),
				'email'             => $this->char(64)->comment('邮箱，第三方仅通讯录应用可获取'),
				'is_leader_in_dept' => $this->char(64)->comment('表示在所在的部门内是否为上级。；第三方仅通讯录应用可获取'),
				'avatar'            => $this->string(255)->comment('头像url。 第三方仅通讯录应用可获取'),
				'thumb_avatar'      => $this->string(255)->comment('头像缩略图url。第三方仅通讯录应用可获取'),
				'telephone'         => $this->char(64)->comment('座机。第三方仅通讯录应用可获取'),
				'enable'            => $this->tinyInteger(1)->unsigned()->comment('成员启用状态。1表示启用的成员，0表示被禁用。注意，服务商调用接口不会返回此字段'),
				'alias'             => $this->char(64)->comment('别名；第三方仅通讯录应用可获取'),
				'address'           => $this->string(255)->comment('地址'),
				'extattr'           => $this->text()->comment('扩展属性，第三方仅通讯录应用可获取'),
				'status'            => $this->tinyInteger(2)->unsigned()->comment('激活状态: 1=已激活，2=已禁用，4=未激活。已激活代表已激活企业微信或已关注微工作台（原企业号）。未激活代表既未激活企业微信又未关注微工作台（原企业号）'),
				'qr_code'           => $this->string(255)->comment('员工个人二维码，扫描可添加为外部联系人(注意返回的是一个url，可在浏览器上打开该url以展示二维码)；第三方仅通讯录应用可获取'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信成员表\'');

			$this->createIndex('KEY_WORK_USER_USERID', '{{%work_user}}', 'userid');

			$this->addForeignKey('KEY_WORK_USER_CORPID', '{{%work_user}}', 'corp_id', '{{%work_corp}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_090746_add_table_work_user cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_090746_add_table_work_user cannot be reverted.\n";

			return false;
		}
		*/
	}
