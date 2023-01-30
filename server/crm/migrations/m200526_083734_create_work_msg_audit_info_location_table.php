<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%work_msg_audit_info_location}}`.
	 * Has foreign keys to the tables:
	 *
	 * - `{{%work_msg_audit_info}}`
	 */
	class m200526_083734_create_work_msg_audit_info_location_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_msg_audit_info_location}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
				'longitude'     => $this->double()->comment('经度'),
				'latitude'      => $this->double()->comment('纬度'),
				'address'       => $this->text()->comment('地址信息'),
				'title'         => $this->string(255)->comment('位置信息的title'),
				'zoom'          => $this->integer(32)->unsigned()->comment('缩放比例'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'位置类型会话消息表\'');

			// creates index for column `audit_info_id`
			$this->createIndex(
				'{{%idx-work_msg_audit_info_location-audit_info_id}}',
				'{{%work_msg_audit_info_location}}',
				'audit_info_id'
			);

			// add foreign key for table `{{%work_msg_audit_info}}`
			$this->addForeignKey(
				'{{%fk-work_msg_audit_info_location-audit_info_id}}',
				'{{%work_msg_audit_info_location}}',
				'audit_info_id',
				'{{%work_msg_audit_info}}',
				'id',
				'CASCADE'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops foreign key for table `{{%work_msg_audit_info}}`
			$this->dropForeignKey(
				'{{%fk-work_msg_audit_info_location-audit_info_id}}',
				'{{%work_msg_audit_info_location}}'
			);

			// drops index for column `audit_info_id`
			$this->dropIndex(
				'{{%idx-work_msg_audit_info_location-audit_info_id}}',
				'{{%work_msg_audit_info_location}}'
			);

			$this->dropTable('{{%work_msg_audit_info_location}}');
		}
	}
