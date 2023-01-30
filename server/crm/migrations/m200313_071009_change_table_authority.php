<?php

use yii\db\Migration;

/**
 * Class m200313_071009_change_table_authority
 */
class m200313_071009_change_table_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%authority}}', 'sort', 'int(11) unsigned NOT NULL COMMENT \'排序\' AFTER `status`');
	    $this->createIndex('KEY_AUTHORITY_SORT', '{{%authority}}', 'sort');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200313_071009_change_table_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200313_071009_change_table_authority cannot be reverted.\n";

        return false;
    }
    */
}
