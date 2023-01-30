<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moments}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_corp}}`
	 * - `{{%work_user}}`
	 */
	class m200825_105344_create_work_moments_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moments}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID'),
				'user_id'     => $this->integer(11)->unsigned()->comment('成员ID'),
				'open_status' => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('可见范围：1、全部；2、标签；3、指定成员'),
				'open_range'  => $this->text()->comment('可见范围标识：open_status为2时是标签ID集合，open_status为3时是成员集合'),
				'type'        => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('类型：1、仅文本；2、图片；3、视频；4、链接'),
				'text'        => $this->text()->comment('文本内容'),
				'can_goods'   => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('是否可以点赞：0、关闭；1、开启'),
				'can_reply'   => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('是否可以回复：0、关闭；1、开启'),
				'open_sum'    => $this->integer(11)->unsigned()->defaultValue(0)->comment('打开次数'),
				'share_sum'   => $this->integer(11)->unsigned()->defaultValue(0)->comment('分享次数'),
				'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('状态：0、删除；1、正常'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈\'');

			// creates index for column `corp_id`
			$this->createIndex(
				'{{%idx-work_moments-corp_id}}',
				'{{%work_moments}}',
				'corp_id'
			);

			// add foreign key for table `{{%work_corp}}`
			$this->addForeignKey(
				'{{%fk-work_moments-corp_id}}',
				'{{%work_moments}}',
				'corp_id',
				'{{%work_corp}}',
				'id',
				'CASCADE'
			);

			// creates index for column `user_id`
			$this->createIndex(
				'{{%idx-work_moments-user_id}}',
				'{{%work_moments}}',
				'user_id'
			);

			// add foreign key for table `{{%work_user}}`
			$this->addForeignKey(
				'{{%fk-work_moments-user_id}}',
				'{{%work_moments}}',
				'user_id',
				'{{%work_user}}',
				'id',
				'CASCADE'
			);

			// creates index for column `open_status`
			$this->createIndex(
				'{{%idx-work_moments-open_status}}',
				'{{%work_moments}}',
				'open_status'
			);

			// creates index for column `type`
			$this->createIndex(
				'{{%idx-work_moments-type}}',
				'{{%work_moments}}',
				'type'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops index for column `type`
			$this->dropIndex(
				'{{%idx-work_moments-type}}',
				'{{%work_moments}}'
			);

			// drops index for column `open_status`
			$this->dropIndex(
				'{{%idx-work_moments-open_status}}',
				'{{%work_moments}}'
			);

			// drops foreign key for table `{{%work_corp}}`
			$this->dropForeignKey(
				'{{%fk-work_moments-corp_id}}',
				'{{%work_moments}}'
			);

			// drops index for column `corp_id`
			$this->dropIndex(
				'{{%idx-work_moments-corp_id}}',
				'{{%work_moments}}'
			);

			// drops foreign key for table `{{%work_user}}`
			$this->dropForeignKey(
				'{{%fk-work_moments-user_id}}',
				'{{%work_moments}}'
			);

			// drops index for column `user_id`
			$this->dropIndex(
				'{{%idx-work_moments-user_id}}',
				'{{%work_moments}}'
			);

			$this->dropTable('{{%work_moments}}');
		}
	}
