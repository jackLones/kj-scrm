<?php

	use yii\db\Migration;

	/**
	 * Class m200318_055834_change_column_location
	 */
	class m200318_055834_change_column_location extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_corp}}', 'sync_user_time', 'int(11) unsigned DEFAULT NULL COMMENT \'最后一次同步通讯录时间\' AFTER `location`');
			$this->addColumn('{{%work_corp}}', 'last_tag_time', 'int(11) unsigned DEFAULT NULL COMMENT \'最后一次同步企业微信标签\' AFTER `sync_user_time`');
			$this->addColumn('{{%work_corp}}', 'last_customer_tag_time', 'int(11) unsigned DEFAULT NULL COMMENT \'最后一次同步客户标签\' AFTER `last_tag_time`');

			$workCorpAuthData = \app\models\WorkCorpAuth::find()->all();
			if (!empty($workCorpAuthData)) {
				/** @var \app\models\WorkCorpAuth $authData */
				foreach ($workCorpAuthData as $authData) {
					\app\models\WorkCorp::updateAll([
						'sync_user_time'         => $authData->sync_user_time,
						'last_tag_time'          => $authData->last_tag_time,
						'last_customer_tag_time' => $authData->last_customer_tag_time,
					], [
						'id' => $authData->corp_id
					]);
				}
			}

			$this->dropColumn('{{%work_corp_auth}}', 'sync_user_time');
			$this->dropColumn('{{%work_corp_auth}}', 'last_tag_time');
			$this->dropColumn('{{%work_corp_auth}}', 'last_customer_tag_time');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200318_055834_change_column_location cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200318_055834_change_column_location cannot be reverted.\n";

			return false;
		}
		*/
	}
