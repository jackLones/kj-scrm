<?php

	use yii\db\Migration;

	/**
	 * Class m210324_111221_change_sub_user_detail_columns
	 */
	class m210324_111221_change_sub_user_detail_columns extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->alterColumn('{{%authority_sub_user_detail}}', 'department', 'longtext COMMENT \'部门\'');
			$this->alterColumn('{{%authority_sub_user_detail}}', 'user_key', 'longtext COMMENT \'可见员工，默认是单人\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->alterColumn('{{%authority_sub_user_detail}}', 'department', 'varchar(255) DEFAULT NULL COMMENT \'部门\'');
			$this->alterColumn('{{%authority_sub_user_detail}}', 'user_key', 'varchar(255) DEFAULT NULL COMMENT \'可见员工，默认是单人\'');
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210324_111221_change_sub_user_detail_columns cannot be reverted.\n";

			return false;
		}
		*/
	}
