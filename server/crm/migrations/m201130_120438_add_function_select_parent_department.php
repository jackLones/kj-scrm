<?php

	use yii\db\Migration;

	/**
	 * Class m201130_120438_add_function_select_parent_department
	 */
	class m201130_120438_add_function_select_parent_department extends Migration
	{
		/**
		 * {@inheritdoc}
		 */
		public function safeUp ()
		{
			$sql = <<<sql
				CREATE FUNCTION `getParentList`(parent_id BIGINT,corp_id BIGINT) RETURNS varchar(10000) CHARSET utf8mb4
				DETERMINISTIC
				BEGIN
							DECLARE pid INT DEFAULT 1;
							DECLARE str VARCHAR(10000) DEFAULT '';
							WHILE parent_id > 0 DO
							SET pid=(SELECT parentid FROM {{%work_department}} WHERE department_id=parent_id and corp_id = corp_id LIMIT 1);
				       IF pid > 0 THEN
									IF str = '' THEN
										SET str = pid;  
									ELSE 
										SET str = concat(str,',',pid);  
									END IF;
				         SET parent_id = pid;
							 ELSE
								 SET parent_id = pid;
				       END IF; 
				   END WHILE;
					RETURN str;
				 END
sql;
			$this->execute($sql);
		}

		/**
		 * {@inheritdoc}
		 */
		public function safeDown ()
		{
			echo "m201130_120438_add_function_select_parent_department cannot be reverted.\n";

			return false;
		}

		/*
		// Use up()/down() to run migration code without a transaction.
		public function up()
		{

		}

		public function down()
		{
			echo "m201130_120438_add_function_select_parent_department cannot be reverted.\n";

			return false;
		}
		*/
	}
