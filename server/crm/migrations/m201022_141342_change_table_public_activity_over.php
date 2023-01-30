<?php

use yii\db\Migration;

/**
 * Class m201022_141342_change_table_public_activity_over
 */
class m201022_141342_change_table_public_activity_over extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$time = date("Y-m-d",strtotime("+1 day"));
		$this->execute("CREATE EVENT `activity` ON SCHEDULE EVERY 1 MINUTE STARTS '$time 00:00:01' ON COMPLETION NOT PRESERVE ENABLE DO CALL activity_over_end ();");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_141342_change_table_public_activity_over cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_141342_change_table_public_activity_over cannot be reverted.\n";

        return false;
    }
    */
}
