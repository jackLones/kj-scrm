<?php

	use yii\db\Migration;

	/**
	 * Class m200512_092113_add_table_queue
	 */
	class m200512_092113_add_table_queue extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<SQL
CREATE TABLE {{%queue}} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` varchar(255) NOT NULL,
  `job` blob NOT NULL,
  `pushed_at` int(11) NOT NULL,
  `ttr` int(11) NOT NULL,
  `delay` int(11) NOT NULL DEFAULT 0,
  `priority` int(11) unsigned NOT NULL DEFAULT 1024,
  `reserved_at` int(11) DEFAULT NULL,
  `attempt` int(11) DEFAULT NULL,
  `done_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel`(128)),
  KEY `reserved_at` (`reserved_at`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='队列表';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200512_092113_add_table_queue cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200512_092113_add_table_queue cannot be reverted.\n";

			return false;
		}
		*/
	}
