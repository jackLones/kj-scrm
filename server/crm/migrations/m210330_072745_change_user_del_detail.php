<?php

	use app\queue\ChangeUserDelJob;
	use yii\db\Migration;

	/**
	 * Class m210330_072745_change_user_del_detail
	 */
	class m210330_072745_change_user_del_detail extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			\Yii::$app->queue->push(new ChangeUserDelJob());
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210330_072745_change_user_del_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210330_072745_change_user_del_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
