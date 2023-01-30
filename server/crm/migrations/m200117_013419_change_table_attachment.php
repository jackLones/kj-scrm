<?php

	use yii\db\Migration;

	/**
	 * Class m200117_013419_change_table_attachment
	 */
	class m200117_013419_change_table_attachment extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%attachment}}', 'material_id', 'int(11) unsigned COMMENT \'素材库id，目前只关联图文\'');
			$this->addColumn('{{%attachment_group}}', 'is_not_group', 'tinyint(1) DEFAULT \'0\' COMMENT \'0已分组、1未分组\'');
			$this->alterColumn('{{%attachment}}', 'appId', 'text COMMENT \'小程序appid\'');
			$this->alterColumn('{{%attachment}}', 'appPath', 'text COMMENT \'小程序page路径\'');
			$this->alterColumn('{{%work_material}}', 'appId', 'text COMMENT \'小程序appid\'');
			$this->alterColumn('{{%work_material}}', 'appPath', 'text COMMENT \'小程序page路径\'');
			$this->addColumn('{{%attachment}}', 'text_content', 'text COMMENT \'纯文本内容，不含标签\' AFTER `content`');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200117_013419_change_table_attachment cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200117_013419_change_table_attachment cannot be reverted.\n";

			return false;
		}
		*/
	}
