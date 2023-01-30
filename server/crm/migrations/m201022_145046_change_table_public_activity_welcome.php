<?php

use yii\db\Migration;

/**
 * Class m201022_145046_change_table_public_activity_welcome
 */
class m201022_145046_change_table_public_activity_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_public_activity}}', 'welcome_help', $this->text()->comment("助力者欢迎语")->after("welcome"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_145046_change_table_public_activity_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_145046_change_table_public_activity_welcome cannot be reverted.\n";

        return false;
    }
    */
}
