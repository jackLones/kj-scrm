<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_media}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_moments}}`
	 */
	class m200825_110141_create_work_moment_media_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_media}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'moment_id'   => $this->integer(11)->unsigned()->comment('朋友圈ID'),
				'sort'        => $this->integer(1)->defaultValue(0)->comment('排序'),
				'local_path'  => $this->string(255)->comment('媒体本地位置'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈媒体表\'');

			// creates index for column `moment_id`
			$this->createIndex(
				'{{%idx-work_moment_media-moment_id}}',
				'{{%work_moment_media}}',
				'moment_id'
			);

			// add foreign key for table `{{%work_moments}}`
			$this->addForeignKey(
				'{{%fk-work_moment_media-moment_id}}',
				'{{%work_moment_media}}',
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
				'{{%fk-work_moment_media-moment_id}}',
				'{{%work_moment_media}}'
			);

			// drops index for column `moment_id`
			$this->dropIndex(
				'{{%idx-work_moment_media-moment_id}}',
				'{{%work_moment_media}}'
			);

			$this->dropTable('{{%work_moment_media}}');
		}
	}
