<?php

use yii\db\Migration;

/**
 * Class m201024_074622_change_moment_replay_content
 */
class m201024_074622_change_moment_replay_content extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_moment_reply}}', 'content', $this->text()->comment('回复内容')->after('openid'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201024_074622_change_moment_replay_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201024_074622_change_moment_replay_content cannot be reverted.\n";

        return false;
    }
    */
}
