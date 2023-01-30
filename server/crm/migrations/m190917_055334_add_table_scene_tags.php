<?php

	use yii\db\Migration;

	/**
	 * Class m190917_055334_add_table_scene_tags
	 */
	class m190917_055334_add_table_scene_tags extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%scene_tags}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'scene_id'    => $this->integer(11)->unsigned()->comment('参数二维码ID'),
				'tag_id'      => $this->integer(11)->unsigned()->comment('标签ID'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'参数二维码标签表\'');

			$this->createIndex('KEY_SCENE_SCENEID', '{{%scene_tags}}', 'scene_id');
			$this->createIndex('KEY_SCENE_TAGID', '{{%scene_tags}}', 'tag_id');

			$this->addForeignKey('KEY_SCENE_SCENEID', '{{%scene_tags}}', 'scene_id', '{{%scene}}', 'id');
			$this->addForeignKey('KEY_SCENE_TAGID', '{{%scene_tags}}', 'tag_id', '{{%tags}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m190917_055334_add_table_scene_tags cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m190917_055334_add_table_scene_tags cannot be reverted.\n";

			return false;
		}
		*/
	}
