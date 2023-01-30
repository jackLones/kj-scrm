<?php

use yii\db\Migration;

/**
 * Class m200305_063044_change_table_sub_user
 */
class m200305_063044_change_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%sub_user}}', 'type', 'tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT \'0子账户1主账户\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200305_063044_change_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200305_063044_change_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
