<?php

	use yii\db\Migration;

	/**
	 * Class m201010_041515_change_work_user_columns
	 */
	class m201010_041515_change_work_user_columns extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%work_user}}', 'department', $this->text()->comment('成员所属部门id列表，仅返回该应用有查看权限的部门id')->after('name'));
			$this->alterColumn('{{%work_user}}', 'order', $this->text()->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。值范围是[0, 2^32)')->after('department'));
			$this->alterColumn('{{%work_user}}', 'position', $this->text()->comment('职务信息；第三方仅通讯录应用可获取')->after('order'));
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->alterColumn('{{%work_user}}', 'department', $this->char(64)->comment('成员所属部门id列表，仅返回该应用有查看权限的部门id')->after('name'));
			$this->alterColumn('{{%work_user}}', 'order', $this->char(64)->comment('部门内的排序值，默认为0。数量必须和department一致，数值越大排序越前面。值范围是[0, 2^32)')->after('department'));
			$this->alterColumn('{{%work_user}}', 'position', $this->char(64)->comment('职务信息；第三方仅通讯录应用可获取')->after('order'));
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201010_041515_change_work_user_columns cannot be reverted.\n";

			return false;
		}
		*/
	}
