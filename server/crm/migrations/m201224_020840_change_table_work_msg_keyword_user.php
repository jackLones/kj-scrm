<?php

use yii\db\Migration;

/**
 * Class m201224_020840_change_table_work_msg_keyword_user
 */
class m201224_020840_change_table_work_msg_keyword_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey("pig_fk-work_msg_keyword_user-external_id", "{{%work_msg_keyword_user}}");
	    $this->addForeignKey("pig_fk-work_msg_keyword_user-external_id", "{{%work_msg_keyword_user}}", "external_id", "{{%work_external_contact}}", "id", "CASCADE", "CASCADE");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201224_020840_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201224_020840_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }
    */
}
