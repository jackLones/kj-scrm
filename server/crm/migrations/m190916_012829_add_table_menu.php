<?php

	use yii\db\Migration;

	/**
	 * Class m190916_012829_add_table_menu
	 */
	class m190916_012829_add_table_menu extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%menu}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'parent_id'   => $this->integer(11)->unsigned()->comment('父级ID'),
				'title'       => $this->char(32)->comment('菜单名称'),
				'icon'        => $this->string(128)->comment('图标样式'),
				'key'         => $this->string(128)->comment('菜单标识'),
				'link'        => $this->string(255)->comment('菜单地址'),
				'level'       => $this->integer(5)->comment('菜单等级'),
				'sort'        => $this->integer(5)->unsigned()->comment('排序'),
				'is_new'      => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否为新菜单，0：否、1：是'),
				'is_hot'      => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('是否为热门菜单。0：否、1：是'),
				'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('菜单状态：0：隐藏、1：显示'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'菜单表\'');

			$this->addForeignKey('KEY_MENU_PARENTID', '{{%menu}}', 'parent_id', '{{%menu}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190916_012829_add_table_menu cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190916_012829_add_table_menu cannot be reverted.\n";

			return false;
		}
		*/
	}
