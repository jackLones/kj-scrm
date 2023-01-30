<?php

	use app\models\UserCorpRelation;
	use app\queue\OpenSubUserJob;
	use yii\db\Migration;

	/**
	 * Class m210409_064308_change_sub_user_status
	 */
	class m210409_064308_change_sub_user_status extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$data = UserCorpRelation::find()->alias("a")
				->leftJoin("{{%work_corp}} as b", "a.corp_id = b.id")
				->where(["corp_type" => "verified"])->select("a.*")->asArray()->all();
			if (!empty($data)) {
				foreach ($data as $record) {
					\Yii::$app->queue->push(new OpenSubUserJob([
						'corp_id' => $record["corp_id"],
						'uid'     => $record["uid"],
					]));
				}
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210409_064308_change_sub_user_status cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210409_064308_change_sub_user_status cannot be reverted.\n";

			return false;
		}
		*/
	}
