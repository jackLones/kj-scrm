<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_corp}}`
	 */
	class m200525_033746_create_work_msg_audit_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'corp_id'     => $this->integer(11)->unsigned()->comment("企业ID"),
				'secret'      => $this->string(255)->comment("聊天内容存档的Secret，可以在企业微信管理端--管理工具--聊天内容存档查看"),
				'key_version' => $this->integer(11)->unsigned()->comment("加密此条消息使用的公钥版本号。Uint32类型"),
				'private_key' => $this->text()->comment("私钥"),
				'status'      => $this->tinyInteger(2)->defaultValue(-1)->comment("是否开启：1、开启；0、关闭；-1、未开启"),
				'create_time' => $this->timestamp()->defaultExpression("CURRENT_TIMESTAMP")->comment("创建时间"),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话存档配置表\'');

			// creates index for column `corp_id`
			$this->createIndex(
				'{{%IDX-MSG_AUDIT-CORP_ID}}',
				'{{%work_msg_audit}}',
				'corp_id'
			);

			// add foreign key for table `{{%work_corp}}`
			$this->addForeignKey(
				'{{%FK-MSG_AUDIT-CORP_ID}}',
				'{{%work_msg_audit}}',
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
				'{{%FK-MSG_AUDIT-CORP_ID}}',
				'{{%work_msg_audit}}'
			);

			// drops index for column `corp_id`
			$this->dropIndex(
				'{{%IDX-MSG_AUDIT-CORP_ID}}',
				'{{%work_msg_audit}}'
			);

			$this->dropTable('{{%work_msg_audit}}');
		}
	}