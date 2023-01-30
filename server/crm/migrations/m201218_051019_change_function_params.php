<?php

use yii\db\Migration;

/**
 * Class m201218_051019_change_function_params
 */
class m201218_051019_change_function_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<sql
				DROP FUNCTION IF EXISTS getParentList;
				CREATE FUNCTION `getParentList`(parent_id BIGINT,corpid BIGINT) RETURNS varchar(10000) CHARSET utf8mb4
				BEGIN
							DECLARE pid INT DEFAULT 1;
							DECLARE str VARCHAR(10000) DEFAULT '';
							WHILE parent_id > 0 DO
							SET pid=(SELECT parentid FROM {{%work_department}} WHERE department_id=parent_id and corp_id = corpid and is_del=0 LIMIT 1);
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
    public function safeDown()
    {
        echo "m201218_051019_change_function_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201218_051019_change_function_params cannot be reverted.\n";

        return false;
    }
    */
}
