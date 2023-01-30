<?php

use yii\db\Migration;

/**
 * Class m200910_072007_change_contact_follow_user
 */
class m200910_072007_change_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_external_contact_follow_user}}', 'oper_userid', 'varchar(64) DEFAULT \'\' COMMENT \'发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid\' AFTER `add_way` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200910_072007_change_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200910_072007_change_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
