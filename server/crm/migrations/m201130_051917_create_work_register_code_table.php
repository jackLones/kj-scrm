<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_register_code}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%provider}}`
	 * - `{{%state}}`
	 * - `{{%register_code}}`
	 */
	class m201130_051917_create_work_register_code_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_register_code}}', [
				'id'                    => $this->primaryKey(11)->unsigned(),
				'provider_id'           => $this->integer(11)->unsigned()->comment('服务商ID'),
				'state'                 => $this->char(8)->comment('用户自定义的状态值。只支持英文字母和数字。若指定该参数，接口 查询注册状态 及 注册完成回调事件 会相应返回该字段值'),
				'register_code'         => $this->string(255)->comment('注册码，只能消费一次。在访问注册链接时消费。'),
				'register_code_expires' => $this->char(16)->comment('register_code有效期，生成链接需要在有效期内点击跳转'),
				'create_time'           => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信注册CODE表\'');

			// creates index for column `provider_id`
			$this->createIndex(
				'{{%idx-work_register_code-provider_id}}',
				'{{%work_register_code}}',
				'provider_id'
			);

			// add foreign key for table `{{%provider}}`
			$this->addForeignKey(
				'{{%fk-work_register_code-provider_id}}',
				'{{%work_register_code}}',
				'provider_id',
				'{{%work_provider_config}}',
				'id',
				'CASCADE'
			);

			// creates index for column `state`
			$this->createIndex(
				'{{%idx-work_register_code-state}}',
				'{{%work_register_code}}',
				'state'
			);

			// creates index for column `register_code`
			$this->createIndex(
				'{{%idx-work_register_code-register_code}}',
				'{{%work_register_code}}',
				'register_code(6)'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%provider}}`
			$this->dropForeignKey(
				'{{%fk-work_register_code-provider_id}}',
				'{{%work_register_code}}'
			);

			// drops index for column `provider_id`
			$this->dropIndex(
				'{{%idx-work_register_code-provider_id}}',
				'{{%work_register_code}}'
			);

			// drops index for column `state`
			$this->dropIndex(
				'{{%idx-work_register_code-state}}',
				'{{%work_register_code}}'
			);

			// drops index for column `register_code`
			$this->dropIndex(
				'{{%idx-work_register_code-register_code}}',
				'{{%work_register_code}}'
			);

			$this->dropTable('{{%work_register_code}}');
		}
	}
