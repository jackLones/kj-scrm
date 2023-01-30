<?php

use yii\db\Migration;

/**
 * Class m200926_071913_change_table_work_contact_way_redpacket
 */
class m200926_071913_change_table_work_contact_way_redpacket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_contact_way_redpacket}}', 'disabled_time', 'int(10) NOT NULL DEFAULT 0 COMMENT \'活动提前结束时间\' AFTER `end_time`');
	    $this->addColumn('{{%work_contact_way_redpacket}}', 'reserve_day', 'int(11) NOT NULL DEFAULT 0 COMMENT \'活动结束后渠道活码保留期（天）\' AFTER `disabled_time`');
	    $this->alterColumn('{{%work_contact_way_redpacket}}', 'redpacket_status', 'tinyint(1) DEFAULT \'1\' COMMENT \'红包活动状态1未发布2已发布3已失效(红包活动)4已失效(渠道活码)5已删除\' AFTER `send_amount`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200926_071913_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200926_071913_change_table_work_contact_way_redpacket cannot be reverted.\n";

        return false;
    }
    */
}
