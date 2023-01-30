<?php

	use yii\db\Migration;

	/**
	 * Class m201102_091318_change_work_moment_base_Key
	 */
	class m201102_091318_change_work_moment_base_Key extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->addForeignKey('KEY_WORK_MOMENTS_BASE_CORP_ID', '{{%work_moments_base}}', 'corp_id', '{{%work_corp}}', 'id');
			$this->addForeignKey('KEY_WORK_MOMENTS_BASE_CORP_AGENT', '{{%work_moments_base}}', 'agent_id', '{{%work_corp_agent}}', 'id');
			$this->addForeignKey('KEY_WORK_MOMENTS_BASE_SUB_ID', '{{%work_moments_base}}', 'sub_id', '{{%sub_user}}', 'sub_id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201102_091318_change_work_moment_base_Key cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201102_091318_change_work_moment_base_Key cannot be reverted.\n";

			return false;
		}
		*/
	}
