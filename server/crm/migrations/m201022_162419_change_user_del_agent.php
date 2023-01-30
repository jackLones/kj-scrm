<?php

use yii\db\Migration;

/**
 * Class m201022_162419_change_user_del_agent
 */
class m201022_162419_change_user_del_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_user_del_follow_user_detail}}', 'agent', $this->integer(11)->unsigned()->comment('应用id')->after('corp_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201022_162419_change_user_del_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201022_162419_change_user_del_agent cannot be reverted.\n";

        return false;
    }
    */
}
