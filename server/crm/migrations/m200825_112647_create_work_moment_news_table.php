<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_news}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_moments}}`
	 */
	class m200825_112647_create_work_moment_news_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_news}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'moment_id'   => $this->integer(11)->unsigned()->comment('朋友圈ID'),
				'title'       => $this->char(64)->comment('图文消息标题'),
				'description' => $this->string(255)->comment('图文消息描述'),
				'url'         => $this->text()->comment('图文消息点击跳转地址'),
				'pic_path'    => $this->text()->comment('图文消息配图的地址'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈图文表\'');

			// creates index for column `moment_id`
			$this->createIndex(
				'{{%idx-work_moment_news-moment_id}}',
				'{{%work_moment_news}}',
				'moment_id'
			);

			// add foreign key for table `{{%work_moments}}`
			$this->addForeignKey(
				'{{%fk-work_moment_news-moment_id}}',
				'{{%work_moment_news}}',
				'moment_id',
				'{{%work_moments}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_moments}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_news-moment_id}}',
				'{{%work_moment_news}}'
			);

			// drops index for column `moment_id`
			$this->dropIndex(
				'{{%idx-work_moment_news-moment_id}}',
				'{{%work_moment_news}}'
			);

			$this->dropTable('{{%work_moment_news}}');
		}
	}
