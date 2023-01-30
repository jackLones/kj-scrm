<?php

	use yii\db\Migration;

	/**
	 * Class m201006_080151_add_table_change_work_moments_setting
	 */
	class m201006_080151_add_table_change_work_moments_setting extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addColumn('{{%work_moment_setting}}', 'heard_img',  $this->string(255)->comment("默认头像"));
			$this->addColumn('{{%work_moment_setting}}', 'is_heard', $this->integer(1)->defaultValue(0)->unsigned()->comment("0不允许修改1允许"));
			$this->addColumn('{{%work_moment_setting}}', 'description', $this->string(255)->comment("个性签名"));
			$this->addColumn('{{%work_moment_setting}}', 'is_description', $this->integer(1)->defaultValue(0)->comment("是否个性签名"));
			$this->addColumn('{{%work_moment_setting}}', 'agent_id', $this->integer(11)->unsigned()->comment("应用id"));
			$this->addForeignKey('KEY_WORK_MOMENT_SETTING_CORP_AGENT', '{{%work_moment_setting}}', 'agent_id', '{{%work_corp_agent}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201006_080151_add_table_change_work_moments_setting cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201006_080151_add_table_change_work_moments_setting cannot be reverted.\n";

			return false;
		}
		*/
	}
