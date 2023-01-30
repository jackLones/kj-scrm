<?php

	use yii\db\Migration;

	/**
	 * Class m200902_124017_change_custom_field
	 */
	class m200902_124017_change_custom_field extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			//微信号
			$customField = \app\models\CustomField::findOne(['uid' => 0, 'type' => 1, 'key' => 'wx_num']);
			if (empty($customField)) {
				$this->insert('{{%custom_field}}', [
					'key'        => 'wx_num',
					'title'      => '微信号',
					'type'       => 1,
					'status'     => 1,
					'is_define'  => 0,
					'updatetime' => time(),
					'createtime' => time(),
				]);
			}
			//线下客户来源
			$customField = \app\models\CustomField::findOne(['uid' => 0, 'type' => 2, 'key' => 'offline_source']);
			if (empty($customField)) {
				$customField             = new \app\models\CustomField();
				$customField->key        = 'offline_source';
				$customField->title      = '线下客户来源';
				$customField->type       = 2;
				$customField->status     = 1;
				$customField->is_define  = 0;
				$customField->updatetime = time();
				$customField->createtime = time();

				if ($customField->validate() && $customField->save()) {
					$this->insert('{{%custom_field_option}}', [
						'fieldid' => $customField->id,
						'value'   => 1,
						'match'   => '客户介绍',
					]);
					$this->insert('{{%custom_field_option}}', [
						'fieldid' => $customField->id,
						'value'   => 2,
						'match'   => '广告宣传',
					]);
					$this->insert('{{%custom_field_option}}', [
						'fieldid' => $customField->id,
						'value'   => 3,
						'match'   => '网上搜索',
					]);
					$this->insert('{{%custom_field_option}}', [
						'fieldid' => $customField->id,
						'value'   => 4,
						'match'   => '陌拜',
					]);
					$this->insert('{{%custom_field_option}}', [
						'fieldid' => $customField->id,
						'value'   => 5,
						'match'   => '其他',
					]);
				}
			}

		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200902_124017_change_custom_field cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200902_124017_change_custom_field cannot be reverted.\n";

			return false;
		}
		*/
	}
