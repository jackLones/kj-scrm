<?php

	use app\models\AuthStoreGroup;
	use app\models\UserCorpRelation;
	use yii\db\Migration;

	/**
	 * Class m210302_015601_change_auth_store_group
	 */
	class m210302_015601_change_auth_store_group extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$data = UserCorpRelation::find()->all();
			foreach ($data as $datum) {
				AuthStoreGroup::CreatNoGroup($datum["uid"], $datum["corp_id"]);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210302_015601_change_auth_store_group cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210302_015601_change_auth_store_group cannot be reverted.\n";

			return false;
		}
		*/
	}
