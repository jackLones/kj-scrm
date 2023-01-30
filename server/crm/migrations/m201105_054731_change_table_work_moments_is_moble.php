<?php

use yii\db\Migration;

/**
 * Class m201105_054731_change_table_work_moments_is_moble
 */
class m201105_054731_change_table_work_moments_is_moble extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%work_moments_base}}", "is_mobile", $this->tinyInteger(1)->unsigned()->comment("来源1pc2手机端")->after("user_id"));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201105_054731_change_table_work_moments_is_moble cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201105_054731_change_table_work_moments_is_moble cannot be reverted.\n";

        return false;
    }
    */
}
