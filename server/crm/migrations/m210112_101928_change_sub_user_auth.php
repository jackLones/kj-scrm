<?php

	use app\models\Authority;
	use app\models\SubUser;
	use app\models\SubUserAuthority;
	use app\models\WorkCorp;
	use app\models\WorkUser;
	use yii\db\Migration;

	/**
	 * Class m210112_101928_change_sub_user_auth
	 */
	class m210112_101928_change_sub_user_auth extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$corp_id = Yii::$app->cache->get("corp_id");
			if (empty($corp_id)) {
				$workCorps = WorkCorp::find()->where(["corp_type" => "verified"])->all();
			} else {
				$workCorps = WorkCorp::find()->where(["corp_type" => "verified"])->andWhere([">", "id", $corp_id])->all();
			}
			$params   = SubUserAuthority::getDisabledParams(1);
			$routeIds = Authority::find()->where(["route" => $params])->select("id")->asArray()->all();
			$routeIds = array_column($routeIds, "id");
			$routeIds = array_values($routeIds);
			foreach ($workCorps as $workCorp) {
				$data = WorkUser::find()->where(["is_external" => 1, "corp_id" => $workCorp->id])->select("mobile")->asArray()->all();
				if (!empty($data)) {
					$data = array_column($data, 'mobile');
					if (!empty($data)) {
						$SubUserData = SubUser::find()->alias("a")
							->leftJoin("{{%sub_user_authority}} as b", "a.sub_id = b.sub_user_id")
							->where(["and", ["in", "a.account", $data], ["b.wx_id" => $workCorp->id, "b.type" => 2, "a.status" => 1]])
							->select("b.id,b.authority_ids")->asArray()->all();
						if (!empty($SubUserData)) {
							foreach ($SubUserData as $record) {
								if (!empty($record["authority_ids"])) {
									$authority_ids = explode(",", $record["authority_ids"]);
									$routeIdsDiff  = array_diff($routeIds, $authority_ids);
									if (!empty($routeIdsDiff)) {
										$routeIdsALL = array_merge($authority_ids, $routeIdsDiff);
										$routeIdsALL = array_values($routeIdsALL);
										$routeIdsALL = implode(",", $routeIdsALL);
										SubUserAuthority::updateAll(["authority_ids" => $routeIdsALL], ["id" => $record["id"]]);
									}
								} else {
									$routeIdsALL = implode(",", $routeIds);
									SubUserAuthority::updateAll(["authority_ids" => $routeIdsALL], ["id" => $record["id"]]);
								}
							}
							Yii::$app->cache->set("corp_id", $workCorp->id, 3600);
						}
					}
				}
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210112_101928_change_sub_user_auth cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210112_101928_change_sub_user_auth cannot be reverted.\n";

			return false;
		}
		*/
	}
