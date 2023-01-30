<?php

use yii\db\Migration;

/**
 * Class m200226_130106_change_table_authority
 */
class m200226_130106_change_table_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%authority}}', 'pid', ' tinyint(2) unsigned DEFAULT NULL COMMENT \'父级id\' after `level` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200226_130106_change_table_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_130106_change_table_authority cannot be reverted.\n";

        return false;
    }
    */
}
