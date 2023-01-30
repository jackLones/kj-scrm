<?php

	use yii\db\Migration;

	/**
	 * Class m210330_013132_add_content_to_radar_link
	 */
	class m210330_013132_add_content_to_radar_link extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			\app\models\RadarLink::allAttachmentAddRadar();
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m210330_013132_add_content_to_radar_link cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m210330_013132_add_content_to_radar_link cannot be reverted.\n";

			return false;
		}
		*/
	}
