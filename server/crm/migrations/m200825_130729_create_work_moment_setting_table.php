<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_moment_setting}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_corp}}`
	 */
	class m200825_130729_create_work_moment_setting_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_moment_setting}}', [
				'id'             => $this->primaryKey(11)->unsigned(),
				'corp_id'        => $this->integer(11)->unsigned()->comment('企业ID'),
				'status'         => $this->tinyInteger(1)->unsigned()->comment('状态：0、关闭；1：开启')->defaultValue(0),
				'banner_type'    => $this->tinyInteger(1)->unsigned()->comment('朋友圈背景图样式：1、统一；2、可以自定义')->defaultValue(1),
				'banner_info'    => $this->text()->comment('背景图设置，最多5个'),
				'can_goods'      => $this->tinyInteger(1)->unsigned()->comment('是否可以点赞：0、关闭；1、开启')->defaultValue(1),
				'can_reply'      => $this->tinyInteger(1)->unsigned()->comment('是否可以评论：0、关闭；1、开启')->defaultValue(1),
				'external_name'  => $this->char(16)->comment('属性名称： 需要先确保在管理端有创建该属性，否则会忽略'),
				'external_title' => $this->char(16)->comment('网页的展示标题'),
				'create_time'    => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信朋友圈企业设置表\'');

			// creates index for column `corp_id`
			$this->createIndex(
				'{{%idx-work_moment_setting-corp_id}}',
				'{{%work_moment_setting}}',
				'corp_id'
			);

			// add foreign key for table `{{%work_corp}}`
			$this->addForeignKey(
				'{{%fk-work_moment_setting-corp_id}}',
				'{{%work_moment_setting}}',
				'corp_id',
				'{{%work_corp}}',
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
				'{{%fk-work_moment_setting-corp_id}}',
				'{{%work_moment_setting}}'
			);

			// drops index for column `corp_id`
			$this->dropIndex(
				'{{%idx-work_moment_setting-corp_id}}',
				'{{%work_moment_setting}}'
			);

			$this->dropTable('{{%work_moment_setting}}');
		}
	}
