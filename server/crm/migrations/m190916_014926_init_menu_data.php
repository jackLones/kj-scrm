<?php

	use yii\db\Migration;

	/**
	 * Class m190916_014926_init_menu_data
	 */
	class m190916_014926_init_menu_data extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->insert('{{%menu}}', [
				'id'        => 1,
				'parent_id' => NULL,
				'title'     => '运营中心',
				'icon'      => 'home',
				'key'       => 'home',
				'link'      => 'home',
				'level'     => 1,
				'sort'      => 1,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 2,
				'parent_id' => NULL,
				'title'     => '智能互动',
				'icon'      => 'message',
				'key'       => 'message',
				'link'      => '',
				'level'     => 1,
				'sort'      => 2,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 3,
				'parent_id' => NULL,
				'title'     => '群发推送',
				'icon'      => 'filter',
				'key'       => 'filter',
				'link'      => '',
				'level'     => 1,
				'sort'      => 3,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 4,
				'parent_id' => NULL,
				'title'     => '粉丝管理',
				'icon'      => 'team',
				'key'       => 'team',
				'link'      => '',
				'level'     => 1,
				'sort'      => 4,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 5,
				'parent_id' => NULL,
				'title'     => '素材库',
				'icon'      => 'hdd',
				'key'       => 'hdd',
				'link'      => '',
				'level'     => 1,
				'sort'      => 5,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 6,
				'parent_id' => 2,
				'title'     => '智能推送',
				'icon'      => '',
				'key'       => 'push',
				'link'      => 'push/list',
				'level'     => 2,
				'sort'      => 1,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 7,
				'parent_id' => 2,
				'title'     => '被关注回复',
				'icon'      => '',
				'key'       => 'reply',
				'link'      => 'reply/list',
				'level'     => 2,
				'sort'      => 2,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 8,
				'parent_id' => 2,
				'title'     => '渠道二维码',
				'icon'      => '',
				'key'       => 'qrcode',
				'link'      => 'qrcode/list',
				'level'     => 2,
				'sort'      => 3,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 9,
				'parent_id' => 3,
				'title'     => '客服消息',
				'icon'      => '',
				'key'       => 'customer',
				'link'      => 'customer/list',
				'level'     => 2,
				'sort'      => 1,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 10,
				'parent_id' => 3,
				'title'     => '模板消息',
				'icon'      => '',
				'key'       => 'template',
				'link'      => 'template/list',
				'level'     => 2,
				'sort'      => 2,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 11,
				'parent_id' => 3,
				'title'     => '高级群发',
				'icon'      => '',
				'key'       => 'senior',
				'link'      => 'senior/list',
				'level'     => 2,
				'sort'      => 3,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 12,
				'parent_id' => 4,
				'title'     => '粉丝消息',
				'icon'      => '',
				'key'       => 'fansMsg',
				'link'      => 'fans/msg',
				'level'     => 2,
				'sort'      => 1,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 13,
				'parent_id' => 4,
				'title'     => '粉丝列表',
				'icon'      => '',
				'key'       => 'fans',
				'link'      => 'fans/list',
				'level'     => 2,
				'sort'      => 2,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 14,
				'parent_id' => 4,
				'title'     => '标签管理',
				'icon'      => '',
				'key'       => 'tags',
				'link'      => 'tags/list',
				'level'     => 2,
				'sort'      => 3,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 15,
				'parent_id' => 5,
				'title'     => '本地素材',
				'icon'      => '',
				'key'       => 'localMaterial',
				'link'      => 'material/local',
				'level'     => 2,
				'sort'      => 1,
			]);

			$this->insert('{{%menu}}', [
				'id'        => 16,
				'parent_id' => 5,
				'title'     => '本地素材',
				'icon'      => '',
				'key'       => 'wxMaterial',
				'link'      => 'material/wx',
				'level'     => 2,
				'sort'      => 2,
			]);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190916_014926_init_menu_data cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190916_014926_init_menu_data cannot be reverted.\n";

			return false;
		}
		*/
	}
