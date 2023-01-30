<?php

use yii\db\Migration;

/**
 * Class m201221_062911_change_work_public_activity_url
 */
class m201221_062911_change_work_public_activity_url extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn("{{%work_public_activity_url}}", "url", $this->text()->comment("原始连接"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201221_062911_change_work_public_activity_url cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_062911_change_work_public_activity_url cannot be reverted.\n";

        return false;
    }
    */
}
