<?php

use yii\db\Migration;

/**
 * Class m201102_071834_change_work_moment_base_agent_user_ds
 */
class m201102_071834_change_work_moment_base_agent_user_ds extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn("{{%work_moments_base}}","agent_id",$this->integer(11)->unsigned()->comment("应用id")->after("corp_id"));
		$this->addColumn("{{%work_moments_base}}","user_ids",$this->text()->comment("归属成员id")->after("ownership"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201102_071834_change_work_moment_base_agent_user_ds cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201102_071834_change_work_moment_base_agent_user_ds cannot be reverted.\n";

        return false;
    }
    */
}
