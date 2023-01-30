<?php

use yii\db\Migration;

/**
 * Class m200418_014730_change_table_template_push_msg
 */
class m200418_014730_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}','template_content','text COMMENT \'模板内容\' AFTER `template_data`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200418_014730_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200418_014730_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
