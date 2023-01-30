<?php

	use yii\db\Migration;

	/**
	 * Class m200103_085103_add_table_department
	 */
	class m200103_085103_add_table_department extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_department}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'corp_id'       => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'department_id' => $this->integer(11)->unsigned()->comment('创建的部门id'),
				'name'          => $this->char(16)->comment('部门名称，此字段从2019年12月30日起，对新创建第三方应用不再返回，2020年6月30日起，对所有历史第三方应用不再返回，后续第三方仅通讯录应用可获取，第三方页面需要通过通讯录展示组件来展示部门名称'),
				'name_en'       => $this->char(16)->comment('英文名称'),
				'parentid'      => $this->integer(11)->unsigned()->comment('父亲部门id。根部门为1'),
				'order'         => $this->integer(11)->unsigned()->comment('在父部门中的次序值。order值大的排序靠前。值范围是[0, 2^32)'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信部门表\'');

			$this->createIndex('KEY_WORK_DEPARTMENT_DEPARTMENTID', '{{%work_department}}', 'department_id');
			$this->createIndex('KEY_WORK_DEPARTMENT_PARENTID', '{{%work_department}}', 'parentid');

			$this->addForeignKey('KEY_WORK_DEPARTMENT_CORPID', '{{%work_department}}', 'corp_id', '{{%work_corp}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_085103_add_table_department cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_085103_add_table_department cannot be reverted.\n";

			return false;
		}
		*/
	}
