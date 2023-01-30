<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_provider_template}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%provider}}`
	 */
	class m201130_125357_create_work_provider_template_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_provider_template}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'provider_id' => $this->integer(11)->unsigned()->comment('服务商ID'),
				'template_id' => $this->string(128)->comment('推广包ID，最长为128个字节'),
				'status'      => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('状态：0、关闭；1、开启'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信推广模板表\'');

			// creates index for column `provider_id`
			$this->createIndex(
				'{{%idx-work_provider_template-provider_id}}',
				'{{%work_provider_template}}',
				'provider_id'
			);

			// add foreign key for table `{{%provider}}`
			$this->addForeignKey(
				'{{%fk-work_provider_template-provider_id}}',
				'{{%work_provider_template}}',
				'provider_id',
				'{{%work_provider_config}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%provider}}`
			$this->dropForeignKey(
				'{{%fk-work_provider_template-provider_id}}',
				'{{%work_provider_template}}'
			);

			// drops index for column `provider_id`
			$this->dropIndex(
				'{{%idx-work_provider_template-provider_id}}',
				'{{%work_provider_template}}'
			);

			$this->dropTable('{{%work_provider_template}}');
		}
	}
