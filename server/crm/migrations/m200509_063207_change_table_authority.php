<?php

use yii\db\Migration;

/**
 * Class m200509_063207_change_table_authority
 */
class m200509_063207_change_table_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%authority}}', 'pid', 'int(11) UNSIGNED NULL DEFAULT NULL COMMENT \'父级id\' AFTER `level`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200509_063207_change_table_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200509_063207_change_table_authority cannot be reverted.\n";

        return false;
    }
    */
}
