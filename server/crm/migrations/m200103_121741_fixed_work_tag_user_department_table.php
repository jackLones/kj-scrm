<?php

	use yii\db\Migration;

	/**
	 * Class m200103_121741_fixed_work_tag_user_department_table
	 */
	class m200103_121741_fixed_work_tag_user_department_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addForeignKey('KEY_WORK_TAG_USER_TAGID', '{{%work_tag_user}}', 'tag_id', '{{%work_tag}}', 'id');

			$this->addForeignKey('KEY_WORK_TAG_DEPARTMENT_TAGID', '{{%work_tag_department}}', 'tag_id', '{{%work_tag}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_121741_fixed_work_tag_user_department_table cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_121741_fixed_work_tag_user_department_table cannot be reverted.\n";

			return false;
		}
		*/
	}
