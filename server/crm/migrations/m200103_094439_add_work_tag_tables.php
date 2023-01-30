<?php

	use yii\db\Migration;

	/**
	 * Class m200103_094439_add_work_tag_tables
	 */
	class m200103_094439_add_work_tag_tables extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_tag}}', [
				'id'      => $this->primaryKey(11)->unsigned(),
				'corp_id' => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'tagid'   => $this->integer(11)->unsigned()->comment('标签id，非负整型，指定此参数时新增的标签会生成对应的标签id，不指定时则以目前最大的id自增'),
				'tagname' => $this->char(32)->comment('标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信标签表\'');

			$this->createIndex('KEY_WORK_TAG_TAGID', '{{%work_tag}}', 'tagid');

			$this->addForeignKey('KEY_WORK_TAG_CORPID', '{{%work_tag}}', 'corp_id', '{{%work_corp}}', 'id');


			$this->createTable('{{%work_tag_user}}', [
				'id'      => $this->primaryKey(11)->unsigned(),
				'tag_id'  => $this->integer(11)->unsigned()->comment('授权的企业的标签ID'),
				'user_id' => $this->integer(11)->unsigned()->comment('授权的企业的成员ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信标签成员表\'');

			$this->addForeignKey('KEY_WORK_TAG_USER_USERID', '{{%work_tag_user}}', 'user_id', '{{%work_user}}', 'id');


			$this->createTable('{{%work_tag_department}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'tag_id'        => $this->integer(11)->unsigned()->comment('授权的企业的标签ID'),
				'department_id' => $this->integer(11)->unsigned()->comment('授权的企业的部门ID'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信标签部门表\'');

			$this->addForeignKey('KEY_WORK_TAG_DEPARTMENT_DEPARTMENTID', '{{%work_tag_department}}', 'department_id', '{{%work_department}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_094439_add_work_tag_tables cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_094439_add_work_tag_tables cannot be reverted.\n";

			return false;
		}
		*/
	}
