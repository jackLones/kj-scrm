<?php

use yii\db\Migration;

/**
 * Class m200623_053244_change_work_follow_msg
 */
class m200623_053244_change_work_follow_msg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_follow_msg}}', 'corp_id', 'int(11) unsigned NOT NULL COMMENT \'企业微信id\' after `uid`');
	    $this->dropColumn('{{%work_follow_msg}}', 'userid');
	    $this->dropColumn('{{%work_follow_msg}}', 'is_del');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_053244_change_work_follow_msg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200623_053244_change_work_follow_msg cannot be reverted.\n";

        return false;
    }
    */
}
