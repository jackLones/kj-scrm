<?php

	use yii\db\Migration;

	/**
	 * Class m201028_091827_add_table_work_group_clock_join
	 */
	class m201028_091827_add_table_work_group_clock_join extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%work_group_clock_join}}', [
				'id'                    => $this->primaryKey(11)->unsigned(),
				'activity_id'           => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('活动ID'),
				'external_id'           => $this->integer(11)->unsigned()->defaultValue(0)->comment('外部联系人id'),
				'openid'                => $this->string(64)->defaultValue('')->comment('openid未知客户'),
				'name'                  => $this->string(32)->defaultValue('')->comment('姓名'),
				'mobile'                => $this->string(32)->defaultValue('')->comment('手机号'),
				'region'                => $this->string(60)->defaultValue('')->comment('地区'),
				'city'                  => $this->string(60)->defaultValue('')->comment('城市'),
				'county'                => $this->string(60)->defaultValue('')->comment('县'),
				'detail'                => $this->string(255)->defaultValue('')->comment('详细地址'),
				'remark'                => $this->string(255)->defaultValue('')->comment('备注'),
				'total_days'            => $this->integer(11)->defaultValue(0)->comment('累计打卡天数'),
				'continue_days'         => $this->integer(11)->defaultValue(0)->comment('连续打卡天数'),
				'history_continue_days' => $this->integer(11)->defaultValue(0)->comment('历史最高连续打卡天数'),
				'create_time'           => $this->integer(11)->unsigned()->comment('创建时间'),
				'last_time'             => $this->integer(11)->unsigned()->comment('最近打卡时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'群打卡活动参与表\'');
			$this->addForeignKey('KEY_WORK_GROUP_CLOCK_JOIN_ACTIVITY_ID', '{{%work_group_clock_join}}', 'activity_id', '{{%work_group_clock_activity}}', 'id');
			$this->addForeignKey('KEY_WORK_GROUP_CLOCK_JOIN_EXTERNAL_ID', '{{%work_group_clock_join}}', 'external_id', '{{%work_external_contact}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201028_091827_add_table_work_group_clock_join cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201028_091827_add_table_work_group_clock_join cannot be reverted.\n";

			return false;
		}
		*/
	}
