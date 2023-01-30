<?php

	use yii\db\Migration;

	/**
	 * Class m200316_065747_add_table_fission
	 */
	class m200316_065747_add_table_fission extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%fission}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'uid'          => $this->integer(11)->unsigned()->comment('用户ID'),
				'title'        => $this->string(32)->comment('活动标题'),
				'is_end'       => $this->tinyInteger(1)->defaultValue(0)->comment('在有效期内，奖品已无库存情况下，活动自动结束'),
				'is_friend'    => $this->tinyInteger(1)->defaultValue(0)->comment('裂变要求:0新好友助力、1全部好友'),
				'is_invalid'   => $this->tinyInteger(1)->defaultValue(0)->comment('删企微好友/被拉黑助力失效是否失效:0否、1是'),
				'is_brush'     => $this->tinyInteger(1)->defaultValue(0)->comment('防刷检测:0否、1是'),
				'brush_rule'   => $this->string(250)->defaultValue('')->comment('防刷检测规则:{"brush_time":"","brush_num":""}'),
				'complete_num' => $this->integer(11)->unsigned()->defaultValue(0)->comment('完成数量'),
				'prize_rule'   => $this->string(250)->defaultValue('')->comment('奖品规则:[{"fission_num":"","prize_name":"","prize_num":""}]'),
				'pic_rule'     => $this->text()->comment('图片规则'),
				'is_option'    => $this->text()->comment('引流成员选项:0选择引流成员、1渠道活码获取引流成员'),
				'user_key'     => $this->text()->comment('引流成员'),
				'user'         => $this->text()->comment('用户userID列表'),
				'corp_id'      => $this->integer(11)->unsigned()->comment('授权的企业ID'),
				'agent_id'     => $this->integer(11)->unsigned()->comment('应用id'),
				'config_id'    => $this->string(64)->defaultValue('')->comment('联系方式的配置id'),
				'qr_code'      => $this->string(255)->defaultValue('')->comment('联系二维码的URL'),
				'state'        => $this->string(64)->defaultValue('')->comment('企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值'),
				'welcome'      => $this->text()->comment('欢迎语'),
				'help_tip'     => $this->text()->comment('收到助力信息'),
				'complete_tip' => $this->text()->comment('任务完成提醒'),
				'end_tip'      => $this->text()->comment('活动结束提醒'),
				'status'       => $this->tinyInteger(1)->defaultValue(1)->comment('状态0删除、1未发布、2已发布、3到期结束、4奖品无库存结束、5、手动提前结束'),
				'update_time'  => $this->timestamp()->comment('更新时间'),
				'start_time'   => $this->timestamp()->comment('开始时间'),
				'end_time'     => $this->timestamp()->comment('结束时间'),
				'create_time'  => $this->timestamp()->comment('创建时间'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'裂变表\'');
			$this->addForeignKey('KEY_FISSION_UID', '{{%fission}}', 'uid', '{{%user}}', 'uid');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200316_065747_add_table_fission cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200316_065747_add_table_fission cannot be reverted.\n";

			return false;
		}
		*/
	}
