<?php

use yii\db\Migration;

/**
 * Class m191014_111522_change_table_template_push_msg
 */
class m191014_111522_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}', 'queue_id', 'int(11) NOT NULL COMMENT \'队列id\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191014_111522_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191014_111522_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
