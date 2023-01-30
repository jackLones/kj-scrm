<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%temp_media}}`.
	 */
	class m200911_121654_create_temp_media_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%temp_media}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'md5'         => $this->string(32)->comment('临时资源的MD5值'),
				'media_id'    => $this->char(12)->comment('临时资源ID'),
				'local_path'  => $this->text()->comment('本地地址'),
				'is_use'      => $this->tinyInteger(1)->comment('是否已经被使用：0、未使用；1、已使用'),
				'use_time'    => $this->timestamp()->comment('使用时间'),
				'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'临时素材表\'');

			// creates index for column `media_id`
			$this->createIndex(
				'{{%idx-temp_media-media_id}}',
				'{{%temp_media}}',
				'media_id(6)'
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			// drops index for column `media_id`
			$this->dropIndex(
				'{{%idx-temp_media-media_id}}',
				'{{%temp_media}}'
			);

			$this->dropTable('{{%temp_media}}');
		}
	}
