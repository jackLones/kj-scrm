<?php

use yii\db\Migration;

/**
 * Class m210113_011536_add_table_work_msg_audit_info_external_redpacket
 */
class m210113_011536_add_table_work_msg_audit_info_external_redpacket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%work_msg_audit_info_external_redpacket}}', [
		    'id'            => $this->primaryKey(11)->unsigned(),
		    'audit_info_id' => $this->integer(11)->unsigned()->comment('会话内容ID'),
		    'type'          => $this->tinyinteger(1)->comment('红包消息类型。1 普通红包、2 拼手气群红包'),
		    'wish'          => $this->char(64)->comment('红包祝福语'),
		    'totalcnt'      => $this->integer(11)->unsigned()->comment('红包总个数'),
		    'totalamount'   => $this->integer(11)->unsigned()->comment('红包总金额。单位为分'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'互通红包类型会话消息表\'');

	    // creates index for column `audit_info_id`
	    $this->createIndex(
		    '{{%idx-work_msg_audit_info_external_redpacket-audit_info_id}}',
		    '{{%work_msg_audit_info_external_redpacket}}',
		    'audit_info_id'
	    );

	    // add foreign key for table `{{%work_msg_audit_info}}`
	    $this->addForeignKey(
		    '{{%fk-work_msg_audit_info_external_redpacket-audit_info_id}}',
		    '{{%work_msg_audit_info_external_redpacket}}',
		    'audit_info_id',
		    '{{%work_msg_audit_info}}',
		    'id',
		    'CASCADE'
	    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210113_011536_add_table_work_msg_audit_info_external_redpacket cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210113_011536_add_table_work_msg_audit_info_external_redpacket cannot be reverted.\n";

        return false;
    }
    */
}
