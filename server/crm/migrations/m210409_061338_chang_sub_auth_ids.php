<?php

	use app\models\SubUserAuthority;
	use yii\db\Migration;

	/**
	 * Class m210409_061338_chang_sub_auth_ids
	 */
	class m210409_061338_chang_sub_auth_ids extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$defaultAuth = SubUserAuthority::getDisabledParams(); //外部联系
			$defaultAuth = implode(",", $defaultAuth);
			$subData     = SubUserAuthority::find()->alias("a")
				->leftJoin("{{%sub_user}} as b", "a.sub_user_id = b.sub_id")
				->leftJoin("{{%work_user}} as c", "b.account = c.mobile")
				->where(["a.type" => 2, "a.authority_ids" => '', "c.is_external" => 1, "c.status" => 1])
				->andWhere([">", "a.create_time", "2021-04-06 00:00:00"])
				->select("a.*")->asArray()->all();
			if (empty($defaultAuth) || empty($subData)) {
				return;
			}
			foreach ($subData as $record) {
				SubUserAuthority::updateAll(["authority_ids" => $defaultAuth], ["id" => $record["id"]]);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210409_061338_chang_sub_auth_ids cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{
	
		}
	
		public function down()
		{
			echo "m210409_061338_chang_sub_auth_ids cannot be reverted.\n";
	
			return false;
		}
		*/
	}
