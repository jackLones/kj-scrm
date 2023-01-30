<?php

	use yii\db\Migration;

	/**
	 * Handles the creation of table `{{%youzan_config}}`.
	 */
	class m200527_075230_create_youzan_config_table extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%youzan_config}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'client_id'     => $this->char(18)->comment('有赞云颁发给开发者的client_id即应用ID 长度18位字母和数字组合的字符串'),
				'client_secret' => $this->char(32)->comment('有赞云颁发给开发者的client_secret即应用密钥 长度32位的字母和数字组合的字符串'),
				'status'        => $this->tinyInteger(1)->defaultValue(1)->comment('状态：0、关闭；1、启用'),
				'create_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'有赞云配置表\'');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			$this->dropTable('{{%youzan_config}}');
		}
	}
