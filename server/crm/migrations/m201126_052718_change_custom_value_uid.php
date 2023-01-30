<?php

	use yii\db\Migration;

	/**
	 * Class m201126_052718_change_custom_value_uid
	 */
	class m201126_052718_change_custom_value_uid extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<sql
			CREATE PROCEDURE customUpdate()
			BEGIN
			DECLARE done BOOLEAN DEFAULT 0;
			DECLARE TempId       INT;
			DECLARE TempUid   INT;
			
			-- 数据填充游标
			DECLARE fillData CURSOR FOR SELECT a.id,c.uid FROM {{%custom_field_value}} as a LEFT JOIN {{%work_external_contact}} as b on a.cid = b.id LEFT JOIN {{%user_corp_relation}} as c on b.corp_id = c.corp_id  WHERE a.type = 1 and b.corp_id is not null and a.uid = 0;
			
			DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
			-- 打开游标
			OPEN fillData;
			REPEAT
			-- 循环游标
			FETCH fillData INTO TempId,TempUid;
			IF done!=1 THEN
					UPDATE {{%custom_field_value}} set uid = TempUid  where id = TempId;
			END IF;
			UNTIL DONE END REPEAT;
			-- 关闭游标
			CLOSE fillData;
			
			END
sql;
			$this->execute($sql);
			$sql2 =<<<sql
		call customUpdate();
sql;
			$this->execute($sql2);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201126_052718_change_custom_value_uid cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201126_052718_change_custom_value_uid cannot be reverted.\n";

			return false;
		}
		*/
	}
