<?php

	use yii\db\Migration;

	/**
	 * Class m190917_054521_change_table_scene
	 */
	class m190917_054521_change_table_scene extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%scene}}', 'author_id', 'int(11) UNSIGNED COMMENT\'公众号ID\' AFTER `id`');
			$this->addColumn('{{%scene}}', 'title', 'char(20) NULL DEFAULT NULL COMMENT\'二维码标题\' AFTER `author_id`');
			$this->addColumn('{{%scene}}', 'status', 'tinyint(1) UNSIGNED DEFAULT 1 COMMENT\'是否启用，1：启用、0：不启用\' AFTER `url`');
			$this->addColumn('{{%scene}}', 'push_type', 'tinyint(1) UNSIGNED DEFAULT 1 COMMENT\'推送方式，1：随机推送一条、2：全部推送\' AFTER `status`');

			$this->addForeignKey('KEY_SCENE_AUTHORID', '{{%scene}}', 'author_id', '{{%wx_authorize}}', 'author_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190917_054521_change_table_scene cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190917_054521_change_table_scene cannot be reverted.\n";

			return false;
		}
		*/
	}
