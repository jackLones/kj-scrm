<?php

use yii\db\Migration;

/**
 * Class m200812_053845_add_table_authority_sub_user_detail
 */
class m200812_053845_add_table_authority_sub_user_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%authority_sub_user_detail}}",[
		    "id"=>$this->primaryKey(11),
		    "corp_id"=>$this->integer(11)->unsigned()->comment("企业微信id"),
		    "sub_id"=>$this->integer(11)->unsigned()->comment("子账户"),
		    "department"=>$this->char(255)->unsigned()->comment("部门"),
		    "user_key"=>$this->char(255)->unsigned()->comment("可见员工，默认是单人"),
		    "type_all"=>$this->integer(2)->unsigned()->defaultValue(2)->comment("1,全部；2、仅自己；3部门、4、指定成员"),
		    "create_time"=>$this->integer(11)->unsigned(),
		    "checked_list"=>$this->text()->unsigned()->comment("选中人"),
	    ],"ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='子账户可见范围表'");
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_SUB_USER_ID', '{{%authority_sub_user_detail}}',
		    'sub_id', '{{%sub_user}}', 'sub_id');
	    $this->addForeignKey('KEY_WORK_TAG_CONTACT_CORP_ID', '{{%authority_sub_user_detail}}',
		    'corp_id', '{{%work_corp}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200812_053845_add_table_authority_sub_user_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200812_053845_add_table_authority_sub_user_detail cannot be reverted.\n";

        return false;
    }
    */
}
