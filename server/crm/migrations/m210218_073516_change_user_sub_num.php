<?php

	use app\models\SubUser;
	use app\models\User;
	use yii\db\Migration;

	/**
	 * Class m210218_073516_change_user_sub_num
	 */
	class m210218_073516_change_user_sub_num extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$Users = User::find()->where(["is_merchant"=>1])->all();
			/**@var $user User * */
			foreach ($Users as $user) {
				$subNum = SubUser::find()->where(["uid" => $user->uid, "status" => 1])->count();
				User::updateAll(["sub_num" => $subNum + 50], ["uid" => $user->uid]);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210218_073516_change_user_sub_num cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210218_073516_change_user_sub_num cannot be reverted.\n";

			return false;
		}
		*/
	}
