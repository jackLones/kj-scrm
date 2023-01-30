<?php

use yii\db\Migration;

/**
 * Class m201113_062836_change_table_work_corp
 */
class m201113_062836_change_table_work_corp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_corp}}', 'unshare_chat', 'tinyint(1) default 0 COMMENT \'不共享所在群1是0否\' AFTER `day_external_money`');
	    $this->addColumn('{{%work_corp}}', 'unshare_follow', 'tinyint(1) default 0 COMMENT \'不共享跟进记录1是0否\' AFTER `unshare_chat`');
	    $this->addColumn('{{%work_corp}}', 'unshare_line', 'tinyint(1) default 0 COMMENT \'不共享互动轨迹1是0否\' AFTER `unshare_follow`');
	    $this->addColumn('{{%work_corp}}', 'unshare_field', 'tinyint(1) default 0 COMMENT \'不共享客户画像1是0否\' AFTER `unshare_line`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201113_062836_change_table_work_corp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201113_062836_change_table_work_corp cannot be reverted.\n";

        return false;
    }
    */
}
