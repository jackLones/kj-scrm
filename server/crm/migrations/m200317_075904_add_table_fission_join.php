<?php

	use yii\db\Migration;

	/**
	 * Class m200317_075904_add_table_fission_detail
	 */
	class m200317_075904_add_table_fission_join extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%fission_join}}', [
				'id'            => $this->primaryKey(11)->unsigned(),
				'uid'           => $this->integer(11)->unsigned()->comment('用户ID'),
				'fid'           => $this->integer(11)->unsigned()->comment('裂变任务id'),
				'parent_id'     => $this->integer(11)->defaultValue(0)->comment('上级外部联系人id'),
				'external_id'   => $this->integer(11)->unsigned()->comment('外部联系人id'),
				'config_id'     => $this->string(64)->defaultValue('')->comment('联系方式的配置id'),
				'qr_code'       => $this->string(255)->defaultValue('')->comment('联系二维码的URL'),
				'state'         => $this->string(64)->defaultValue('')->comment('企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'help_num'      => $this->integer(11)->unsigned()->defaultValue(0)->comment('有效助力数'),
				'fission_num'   => $this->integer(11)->comment('裂变人数'),
				'status'        => $this->tinyInteger(1)->defaultValue(0)->comment('任务状态0未完成、1进行中、2已完成'),
				'prize_status'  => $this->tinyInteger(1)->defaultValue(0)->comment('奖品状态0未处理、1已处理、2无法处理'),
				'is_black'      => $this->tinyInteger(1)->defaultValue(0)->comment('是否黑名单0否、1是'),
				'update_time'   => $this->timestamp()->comment('更新时间'),
				'join_time'     => $this->timestamp()->comment('参与时间'),
				'complete_time' => $this->timestamp()->comment('完成时间'),
				'black_time'    => $this->timestamp()->comment('拉入黑名单时间'),
				'is_remind'    => $this->tinyInteger(1)->defaultValue(0)->comment('是否需要提醒：0否、1是'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'裂变参与表\'');
			$this->addForeignKey('KEY_FISSION_JOIN_UID', '{{%fission_join}}', 'uid', '{{%user}}', 'uid');
			$this->addForeignKey('KEY_FISSION_JOIN_FID', '{{%fission_join}}', 'fid', '{{%fission}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200317_075904_add_table_fission_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200317_075904_add_table_fission_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
