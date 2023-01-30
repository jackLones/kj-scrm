<?php

use yii\db\Migration;

/**
 * Class m201010_065301_change_table_money_order
 */
class m201010_065301_change_table_money_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
UPDATE {{%money_order}} SET `status`=1 WHERE ispay=1;
UPDATE {{%money_order}} SET `status`=4 WHERE ispay=0;
SQL;
	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201010_065301_change_table_money_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201010_065301_change_table_money_order cannot be reverted.\n";

        return false;
    }
    */
}
