<?php

	use app\models\Authority;
	use app\models\Menu;
	use app\models\SubUserAuthority;
	use yii\db\Migration;

	/**
	 * Class m210318_064232_change_auth_ids
	 */
	class m210318_064232_change_auth_ids extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$data = ["redForNewList", "redForNewRule"];
			/**权限**/
			$dataAuth = Authority::find()->where(["in", "route", $data])->all();
			$Auth     = Authority::find()->where(["route" => "drainage"])->one();
			/**菜单**/
			$drainage = Menu::find()->where(["key" => "drainage"])->one();
			$menuAuth = Menu::find()->where(["in", "key", $data])->all();
			if (empty($dataAuth) || empty($Auth) || empty($menuAuth) || empty($drainage)) {
				return;
			}
			$menuShort = Menu::find()->where(["parent_id" => $drainage->id])->orderBy(["sort" => SORT_DESC])->one();
			/**@var $authority Authority* */
			foreach ($dataAuth as $authority) {
				$authority->pid = $Auth->id;
				if ($authority->route == 'redForNewList') {
					$authority->name = "红包拉新";
				}
				$authority->save();
			}
			$short = empty($menuShort->sort) ? 0 : $menuShort->sort;
			/**@var $menu Menu* */
			foreach ($menuAuth as $menu) {
				if ($menu->key == 'redForNewList') {
					$menu->title = "红包拉新";
				}
				$menu->parent_id = $drainage->id;
				$short           = $menu->sort = $short + 1;
				$menu->save();
			}
			$delAuth         = Authority::find()->where(["route" => "redForNew"])->one();
			$delAuth->status = 1;
			$delAuth->save();
			$dataSubUser = SubUserAuthority::find()->where("FIND_IN_SET(" . $Auth->id . ",authority_ids)")->all();
			if (!empty($dataSubUser)) {
				/**@var $subUser SubUserAuthority* */
				foreach ($dataSubUser as $subUser) {
					if (!empty($subUser->authority_ids)) {
						$authority_ids = explode(",", $subUser->authority_ids);
						$key           = array_search($Auth->id, $authority_ids);
						if ($key !== false) {
							unset($authority_ids[$key]);
						}
						$subUser->authority_ids = implode(",", $authority_ids);
						$subUser->save();
					}
				}
			}

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210318_064232_change_auth_ids cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210318_064232_change_auth_ids cannot be reverted.\n";

			return false;
		}
		*/
	}
