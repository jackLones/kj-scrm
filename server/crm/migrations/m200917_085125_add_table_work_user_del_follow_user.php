<?php

use yii\db\Migration;

/**
 * Class m200917_085125_add_table_work_user_del_follow_user
 */
class m200917_085125_add_table_work_user_del_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%work_user_del_follow_user}}", [
		    "id"              => $this->primaryKey(11)->unsigned(),
		    "corp_id"         => $this->integer(11)->unsigned()->comment("企业应用id"),
		    "agent"           => $this->integer(11)->unsigned()->comment("1全部"),
		    "type"            => $this->integer(1)->unsigned()->comment("1全部2部门3员工"),
		    "user_id"         => $this->integer(11)->unsigned()->comment("员工id"),
		    "department"      => $this->text()->unsigned()->comment("部门id"),
		    "inform_user"     => $this->text()->unsigned()->comment("可看员工删除被通知人"),
		    "inform_user_key" => $this->text()->unsigned()->comment("可看员工删除被通知人old"),
		    "open_status"     => $this->integer(1)->unsigned()->comment("状态"),
		    "frequency"       => $this->char(11)->unsigned()->comment("频率1每次2每天早上9点汇总"),
		    "create_time"     => $this->integer(11)->unsigned(),
		    "update_time"     => $this->integer(11)->unsigned(),
	    ], "ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工删除外部联系人通知'");
	    $this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_USER_CORP_ID', '{{%work_user_del_follow_user}}', 'corp_id', '{{%work_corp}}', 'id');
	    $this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_USER_USER_ID', '{{%work_user_del_follow_user}}', 'user_id', '{{%work_user}}', 'id');
	    $this->addForeignKey('KEY_WORK_USER_DEL_FOLLOW_USER_AGENT', '{{%work_user_del_follow_user}}', 'agent', '{{%work_corp_agent}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_085125_add_table_work_user_del_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_085125_add_table_work_user_del_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
