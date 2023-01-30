<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_user_config}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_corp}}`
	 * - `{{%work_user}}`
	 */
	class m200825_131028_create_work_moment_user_config_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_user_config}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
				'heard'       => $this->char(255)->unsigned()->comment('自定义头像'),
				'banner_info' => $this->text()->comment('背景图设置，最多5个'),
				'description' => $this->char(64)->comment('签名'),
				'status'      => $this->tinyInteger(1)->unsigned()->comment('状态：0、关闭；1：开启')->defaultValue(0),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈开启成员表\'');

			// creates index for column `corp_id`
			$this->createIndex(
				'{{%idx-work_moment_user_config-corp_id}}',
				'{{%work_moment_user_config}}',
				'corp_id'
			);

			// add foreign key for table `{{%work_corp}}`
			$this->addForeignKey(
				'{{%fk-work_moment_user_config-corp_id}}',
				'{{%work_moment_user_config}}',
				'corp_id',
				'{{%work_corp}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_moment_user_config-user_id}}',
				'{{%work_moment_user_config}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_moment_user_config-user_id}}',
				'{{%work_moment_user_config}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_corp}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_user_config-corp_id}}',
				'{{%work_moment_user_config}}'
			);

			// drops index for column `corp_id`
			$this->dropIndex(
				'{{%idx-work_moment_user_config-corp_id}}',
				'{{%work_moment_user_config}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_user_config-user_id}}',
				'{{%work_moment_user_config}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_moment_user_config-user_id}}',
				'{{%work_moment_user_config}}'
			);

			$this->dropTable('{{%work_moment_user_config}}');
		}
	}
