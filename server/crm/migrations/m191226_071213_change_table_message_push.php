<?php

use yii\db\Migration;

/**
 * Class m191226_071213_change_table_message_push
 */
class m191226_071213_change_table_message_push extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%message_push}}', 'template_id', 'int(11) unsigned DEFAULT \'0\' COMMENT \'短信模版id\' AFTER `type_id` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191226_071213_change_table_message_push cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191226_071213_change_table_message_push cannot be reverted.\n";

        return false;
    }
    */
}
