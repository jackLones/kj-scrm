<?php

use yii\db\Migration;

/**
 * Class m200914_024654_change_table_work_public_activity_function
 */
class m200914_024654_change_table_work_public_activity_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$sql = <<<SQL
			CREATE PROCEDURE `activity_over_end`()
			BEGIN
			DECLARE done BOOLEAN DEFAULT 0;
			DECLARE tmp_end_time INT;
			DECLARE tmp_is_over INT;
			DECLARE tmp_id INT;
			
			DECLARE activity CURSOR FOR SELECT end_time,is_over,id FROM {{%work_public_activity}} where end_time < UNIX_TIMESTAMP() and is_over = 1;
			DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
			
			OPEN activity;
			REPEAT
			FETCH activity INTO tmp_end_time,tmp_is_over,tmp_id;
			IF done!=1 THEN
					UPDATE {{%work_public_activity}} set is_over = 2  where id = tmp_id;
			END IF;
			UNTIL DONE END REPEAT;
			CLOSE activity;
			END
SQL;
		$this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_024654_change_table_work_public_activity_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_024654_change_table_work_public_activity_function cannot be reverted.\n";

        return false;
    }
    */
}
