<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_goods}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_moments}}`
	 * - `{{%work_user}}`
	 * - `{{%work_external_contact}}`
	 */
	class m200825_111857_create_work_moment_goods_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_goods}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'moment_id'   => $this->integer(11)->unsigned()->comment('朋友圈ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
				'external_id' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
				'openid'      => $this->char(64)->comment('外部非联系人openid'),
				'status'      => $this->tinyInteger(1)->unsigned()->comment('状态：0、取消点赞；1：点赞'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈点赞详情表\'');

			// creates index for column `moment_id`
			$this->createIndex(
				'{{%idx-work_moment_goods-moment_id}}',
				'{{%work_moment_goods}}',
				'moment_id'
			);

			// add foreign key for table `{{%work_moments}}`
			$this->addForeignKey(
				'{{%fk-work_moment_goods-moment_id}}',
				'{{%work_moment_goods}}',
				'moment_id',
				'{{%work_moments}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_moment_goods-user_id}}',
				'{{%work_moment_goods}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_moment_goods-user_id}}',
				'{{%work_moment_goods}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `external_id`
			$this->createIndex(
				'{{%idx-work_moment_goods-external_id}}',
				'{{%work_moment_goods}}',
				'external_id'
			);

			// add foreign key for table `{{%work_external_contact}}`
			$this->addForeignKey(
				'{{%fk-work_moment_goods-external_id}}',
				'{{%work_moment_goods}}',
				'external_id',
				'{{%work_external_contact}}',
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
				'{{%fk-work_moment_goods-moment_id}}',
				'{{%work_moment_goods}}'
			);

			// drops index for column `moment_id`
			$this->dropIndex(
				'{{%idx-work_moment_goods-moment_id}}',
				'{{%work_moment_goods}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_goods-user_id}}',
				'{{%work_moment_goods}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_moment_goods-user_id}}',
				'{{%work_moment_goods}}'
			);

			// drops foreign key for table `{{%work_external_contact}}`
			$this->dropForeignKey(
				'{{%fk-work_moment_goods-external_id}}',
				'{{%work_moment_goods}}'
			);

			// drops index for column `external_id`
			$this->dropIndex(
				'{{%idx-work_moment_goods-external_id}}',
				'{{%work_moment_goods}}'
			);

			$this->dropTable('{{%work_moment_goods}}');
		}
	}
