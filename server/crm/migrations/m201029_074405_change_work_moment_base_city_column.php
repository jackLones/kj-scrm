<?php

use yii\db\Migration;

/**
 * Class m201029_074405_change_work_moment_base_city_column
 */
class m201029_074405_change_work_moment_base_city_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn("{{%work_moments_base}}","province",$this->string(20)->defaultValue(NULL)->comment("省"));
    	$this->addColumn("{{%work_moments_base}}","city",$this->string(20)->defaultValue(NULL)->comment("市"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201029_074405_change_work_moment_base_city_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201029_074405_change_work_moment_base_city_column cannot be reverted.\n";

        return false;
    }
    */
}
