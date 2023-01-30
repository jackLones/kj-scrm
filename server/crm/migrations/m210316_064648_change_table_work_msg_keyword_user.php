<?php

use yii\db\Migration;

/**
 * Class m210316_064648_change_table_work_msg_keyword_user
 */
class m210316_064648_change_table_work_msg_keyword_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_msg_keyword_user}}', 'keyword_tag_id', 'varchar(500) DEFAULT \'0\' COMMENT \'推荐规则关联标签表ID\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210316_064648_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210316_064648_change_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }
    */
}
