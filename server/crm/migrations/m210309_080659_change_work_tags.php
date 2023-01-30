<?php

	use app\models\WorkTag;
	use yii\db\Migration;

	/**
	 * Class m210309_080659_change_work_tags
	 */
	class m210309_080659_change_work_tags extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$res = WorkTag::find()->alias("a")
				->leftJoin("{{%work_tag_group}} as b", "a.group_id = b.id")
				->where(["is_del" => 0])
				->select("a.id,b.id as ids,a.corp_id")->asArray()->all();
			foreach ($res as $re) {
				if (empty($re["ids"])) {
					WorkTag::updateAll(["is_del" => 1], ["id" => $re["id"]]);
				}
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210309_080659_change_work_tags cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210309_080659_change_work_tags cannot be reverted.\n";

			return false;
		}
		*/
	}
