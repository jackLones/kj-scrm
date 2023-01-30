<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%user_youzan_relation}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%user}}`
	 * - `{{%youzan_shop}}`
	 */
	class m200527_084139_create_user_youzan_relation_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%user_youzan_relation}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'uid'         => $this->integer(11)->unsigned()->comment('用户ID'),
				'youzan_id'   => $this->integer(11)->unsigned()->comment('有赞店铺ID'),
				'update_time' => $this->timestamp()->comment('更新时间'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'用户有赞关系表\'');

			// creates index for column `uid`
			$this->createIndex(
				'{{%idx-user_youzan_relation-uid}}',
				'{{%user_youzan_relation}}',
				'uid'
			);

			// add foreign key for table `{{%user}}`
			$this->addForeignKey(
				'{{%fk-user_youzan_relation-uid}}',
				'{{%user_youzan_relation}}',
				'uid',
				'{{%user}}',
				'uid',
				'CASCADE'
			);

			// creates index for column `youzan_id`
			$this->createIndex(
				'{{%idx-user_youzan_relation-youzan_id}}',
				'{{%user_youzan_relation}}',
				'youzan_id'
			);

			// add foreign key for table `{{%youzan_shop}}`
			$this->addForeignKey(
				'{{%fk-user_youzan_relation-youzan_id}}',
				'{{%user_youzan_relation}}',
				'youzan_id',
				'{{%youzan_shop}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%user}}`
			$this->dropForeignKey(
				'{{%fk-user_youzan_relation-uid}}',
				'{{%user_youzan_relation}}'
			);

			// drops index for column `uid`
			$this->dropIndex(
				'{{%idx-user_youzan_relation-uid}}',
				'{{%user_youzan_relation}}'
			);

			// drops foreign key for table `{{%youzan_shop}}`
			$this->dropForeignKey(
				'{{%fk-user_youzan_relation-youzan_id}}',
				'{{%user_youzan_relation}}'
			);

			// drops index for column `youzan_id`
			$this->dropIndex(
				'{{%idx-user_youzan_relation-youzan_id}}',
				'{{%user_youzan_relation}}'
			);

			$this->dropTable('{{%user_youzan_relation}}');
		}
	}
