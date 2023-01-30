<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_category}}`.
	 */
	class m200619_014638_create_work_msg_audit_category_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_category}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'category_type' => $this->char(16)->comment('类别标识'),
				'category_name' => $this->char(16)->comment('类别名称'),
				'status'        => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('状态：0、关闭；1、开启'),
				'create_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'会话存档类别表\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropTable('{{%work_msg_audit_category}}');
		}
	}
