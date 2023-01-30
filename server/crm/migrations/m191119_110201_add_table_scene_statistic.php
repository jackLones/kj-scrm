<?php

	use yii\db\Migration;

	/**
	 * Class m191119_110201_add_table_scene_statistic
	 */
	class m191119_110201_add_table_scene_statistic extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$this->createTable('{{%scene_statistic}}', [
				'id'           => $this->primaryKey(11)->unsigned(),
				'scene_id'     => $this->integer(11)->unsigned()->comment('参数二维码ID'),
				'scan_times'   => $this->integer(11)->unsigned()->comment('扫码次数'),
				'scan_num'     => $this->integer(11)->unsigned()->comment('扫码人数'),
				'subscribe'    => $this->integer(11)->unsigned()->comment('新增粉丝数'),
				'unsubscribe'  => $this->integer(11)->unsigned()->comment('流失粉丝数'),
				'net_increase' => $this->integer(11)->comment('净增粉丝数'),
				'data_time'    => $this->date()->comment('统计时间')
			], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道二维码统计表\'');
			$this->createIndex('KEY_SCENE_STATISTIC_SCENEID', '{{%scene_statistic}}', 'scene_id');
			$this->addForeignKey('KEY_SCENE_STATISTIC_SCENEID', '{{%scene_statistic}}', 'scene_id', '{{%scene}}', 'id');
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m191119_110201_add_table_scene_statistic cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m191119_110201_add_table_scene_statistic cannot be reverted.\n";

			return false;
		}
		*/
	}
