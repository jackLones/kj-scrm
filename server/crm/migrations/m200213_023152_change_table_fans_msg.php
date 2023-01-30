<?php

use yii\db\Migration;

/**
 * Class m200213_023152_change_table_fans_msg
 */
class m200213_023152_change_table_fans_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans_msg}}', 'attachment_id', 'int(11) unsigned DEFAULT NULL COMMENT \'附件id\' AFTER `material_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200213_023152_change_table_fans_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200213_023152_change_table_fans_msg cannot be reverted.\n";

        return false;
    }
    */
}
