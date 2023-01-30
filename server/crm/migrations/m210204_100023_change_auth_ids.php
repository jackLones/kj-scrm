<?php

	use app\models\Authority;
	use yii\db\Migration;

	/**
	 * Class m210204_100023_change_auth_ids
	 */
	class m210204_100023_change_auth_ids extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{

			$arr = [
				"account"          => ["account-add", "account-list"],
				"circleOfFriends"  => ["circleOfFriends-list", "circleOfFriends-add", "circleOfFriends-edit", "circleOfFriends-delete", "circleOfFriends-set", "circleOfFriends-address", "circleOfFriends-detail", "circleOfFriends-examine"],
				"welcome"          => ["welcome-list", "welcome-add", "welcome-delete", "welcome-edit"],
				"massMessage"      => ["group-sending-list", "group-sending-add", "group-sending-record", "group-sending-delete"],
				"agent"            => ["agent-list", "agent-add"],
				"wechatManagement" => ["work-management-list", "work-management-add"],
				"mini"             => ["mini-list", "mini-add"],
				"redirect"         => ["redirect-add", "redirect-close", "redirect-delete", "redirect-list"],
				"subAccount"       => ["subAccount-status", "subAccount-modify-pass", "subAccount-modify", "subAccount-info", "subAccount-add", "subAccount-list"],
			];
			foreach ($arr as $key => $item) {
				$kAuth = Authority::find()->where(["status" => 0, "route" => $key])->one();
				if (!empty($kAuth)) {
					$vAuth = Authority::find()->where(["and", ["status" => 0], ["in", "route", $item]])->all();
					/**@var $record Authority* */
					foreach ($vAuth as $record) {
						$record->pid   = $kAuth->id;
						$record->level = $kAuth->level + 1;
						$record->save();
					}
				}
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210204_100023_change_auth_ids cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210204_100023_change_auth_ids cannot be reverted.\n";

			return false;
		}
		*/
	}
