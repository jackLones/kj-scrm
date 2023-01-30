<?php

use yii\db\Migration;

/**
 * Class m200806_043531_add_table_work_external_contact_user_way_detail
 */
class m200806_043531_add_table_work_external_contact_user_way_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%work_external_contact_user_way_detail}}",[
		    "id"=>$this->primaryKey(11)->unsigned(),
		    "way_id"=>$this->integer(11)->unsigned()->comment("群聊活码ID"),
		    "chat_id"=>$this->integer(11)->unsigned()->comment("群聊id"),
		    "way_list_id"=>$this->integer(11)->unsigned()->comment("活码群聊对应表ID"),
		    "user_id"=>$this->integer(11)->unsigned()->comment("企业发送人"),
		    "external_id"=>$this->integer(11)->unsigned()->comment("外部接入人"),
		    "create_time"=>$this->integer(11)->unsigned()->comment("创建时间"),
	    ],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='活码发送明细表'");
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_CHAT_ID', '{{%work_external_contact_user_way_detail}}',
		    'chat_id', '{{%work_chat}}', 'id');
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_WAY_ID', '{{%work_external_contact_user_way_detail}}',
		    'way_id', '{{%work_chat_contact_way}}', 'id');
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_USER_ID', '{{%work_external_contact_user_way_detail}}',
		    'user_id', '{{%work_user}}', 'id');
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_EX_ID', '{{%work_external_contact_user_way_detail}}',
		    'external_id', '{{%work_external_contact}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200806_043531_add_table_work_external_contact_user_way_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200806_043531_add_table_work_external_contact_user_way_detail cannot be reverted.\n";

        return false;
    }
    */
}
