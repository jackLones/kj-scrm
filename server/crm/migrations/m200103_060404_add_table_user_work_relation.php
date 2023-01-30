<?php

	use yii\db\Migration;

	/**
	 * Class m200103_060404_add_table_user_work_relation
	 */
	class m200103_060404_add_table_user_work_relation extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<SQL
DROP TABLE IF EXISTS {{%user_corp_relation}};
CREATE TABLE {{%user_corp_relation}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户ID',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权ID',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `create_time` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  CONSTRAINT `KEY_USER_CORP_RELATION_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_USER_CORP_RELATION_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) COMMENT='用户企业微信关系表';
SQL;

			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m200103_060404_add_table_user_work_relation cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m200103_060404_add_table_user_work_relation cannot be reverted.\n";

			return false;
		}
		*/
	}
