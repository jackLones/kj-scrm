<?php

	use yii\db\Migration;

	/**
	 * Class m200317_091710_add_table_fission_help_detail
	 */
	class m200317_091710_add_table_fission_help_detail extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%fission_help_detail}}', [
				'id'          => $this->primaryKey(11)->unsigned(),
				'fid'         => $this->integer(11)->unsigned()->comment('裂变任务id'),
				'jid'         => $this->integer(11)->unsigned()->comment('参与表id'),
				'external_id' => $this->integer(11)->unsigned()->comment('外部联系人id'),
				'status'      => $this->tinyInteger(1)->defaultValue(1)->comment('是否是有效助力0否、1是'),
				'help_time'   => $this->dateTime()->comment('助力时间'),
				'is_remind'   => $this->tinyInteger(1)->defaultValue(0)->comment('是否需要提醒：0否、1是'),
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'裂变参与助力表\'');
			$this->addForeignKey('KEY_FISSION_HELP_DETAIL_FID', '{{%fission_help_detail}}', 'fid', '{{%fission}}', 'id');
			$this->addForeignKey('KEY_FISSION_HELP_DETAIL_JID', '{{%fission_help_detail}}', 'jid', '{{%fission_join}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200317_091710_add_table_fission_help_detail cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200317_091710_add_table_fission_help_detail cannot be reverted.\n";

			return false;
		}
		*/
	}
