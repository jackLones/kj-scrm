<?php

	use yii\db\Migration;

	/**
	 * Handles adding columns to table `{{%work_external_contact_follow_user}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%index}}`
	 */
	class m200615_083935_add_add_way_columns_to_work_external_contact_follow_user_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_external_contact_follow_user}}', 'add_way', $this->tinyInteger(3)->unsigned()->defaultValue(0)->comment('添加客户的来源：0、未知来源；1、 扫描二维码；2、搜索手机号；3、名片分享；4、群聊；5、手机通讯录；6、微信联系人；7、来自微信的添加好友申请；8、安装第三方应用时自动添加的客服人员；9、搜索邮箱；201、内部成员共享；202、管理员/负责人分配')->after('way_id'));

			// creates index for column `add_way`
			$this->createIndex(
				'{{%idx-work_external_contact_follow_user-add_way}}',
				'{{%work_external_contact_follow_user}}',
				'add_way'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops index for column `add_way`
			$this->dropIndex(
				'{{%idx-work_external_contact_follow_user-add_way}}',
				'{{%work_external_contact_follow_user}}'
			);

			$this->dropColumn('{{%work_external_contact_follow_user}}', 'add_way');
		}
	}
