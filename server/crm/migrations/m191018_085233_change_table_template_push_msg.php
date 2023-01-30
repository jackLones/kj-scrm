<?php

use yii\db\Migration;

/**
 * Class m191018_085233_change_table_template_push_msg
 */
class m191018_085233_change_table_template_push_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%template_push_msg}}', 'will_fans_num', 'int(11) NOT NULL COMMENT \'预计发生粉丝数\' AFTER `fans_num` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191018_085233_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191018_085233_change_table_template_push_msg cannot be reverted.\n";

        return false;
    }
    */
}
