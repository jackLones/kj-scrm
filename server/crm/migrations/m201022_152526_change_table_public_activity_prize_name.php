<?php

use yii\db\Migration;

/**
 * Class m201022_152526_change_table_public_activity_prize_name
 */
class m201022_152526_change_table_public_activity_prize_name extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_public_activity_prize_user}}', 'price', $this->integer(11)->comment("金额")->after("order_sn"));
	    $this->addColumn('{{%work_public_activity_prize_user}}', 'name', $this->string(60)->comment("姓名")->after("price"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_152526_change_table_public_activity_prize_name cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_152526_change_table_public_activity_prize_name cannot be reverted.\n";

        return false;
    }
    */
}
