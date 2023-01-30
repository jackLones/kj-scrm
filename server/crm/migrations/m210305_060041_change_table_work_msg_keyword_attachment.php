<?php

use yii\db\Migration;

/**
 * Class m210305_060041_change_table_work_msg_keyword_attachment
 */
class m210305_060041_change_table_work_msg_keyword_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_msg_keyword_attachment}}', 'type', 'tinyint(1) NOT NULL DEFAULT \'1\' COMMENT \'推送类型1不限制2用户标签\' AFTER `keywords`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_060041_change_table_work_msg_keyword_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_060041_change_table_work_msg_keyword_attachment cannot be reverted.\n";

        return false;
    }
    */
}
