<?php

use yii\db\Migration;

/**
 * Class m201228_084348_change_table_public_sea_customer
 */
class m201228_084348_change_table_public_sea_customer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag_follow_user}}', 'add_time', 'int(11) DEFAULT 0 COMMENT \'添加时间\'');
	    $this->addColumn('{{%work_tag_follow_user}}', 'update_time', 'int(11) DEFAULT 0 COMMENT \'修改时间\'');
	    $this->addColumn('{{%work_corp}}', 'is_return', 'tinyint(1) DEFAULT 1 COMMENT \'同一客户归属多个员工跟进时，是否能退回公海池\'');
	    $this->addColumn('{{%work_corp}}', 'is_sea_info', 'tinyint(1) DEFAULT 1 COMMENT \'是否同步非企微用户画像\'');
	    $this->addColumn('{{%work_corp}}', 'is_sea_tag', 'tinyint(1) DEFAULT 1 COMMENT \'是否同步非企微客户标签\'');
	    $this->addColumn('{{%work_corp}}', 'is_sea_follow', 'tinyint(1) DEFAULT 1 COMMENT \'是否同步非企微跟进记录\'');
	    $this->addColumn('{{%work_corp}}', 'is_sea_phone', 'tinyint(1) DEFAULT 1 COMMENT \'是否同步非企微通话记录\'');
	    $this->addColumn('{{%public_sea_claim_user}}', 'claim_str', 'varchar(255) NOT NULL DEFAULT \'\' COMMENT \'成员添加后的轨迹\'');
	    $this->addColumn('{{%public_sea_contact_follow_user}}', 'follow_user_id', 'int(11) DEFAULT 0 COMMENT \'绑定的企微客户关系表id\'');
	    $this->createIndex("KEY_PUBLIC_SEA_CONTACT_FOLLOW_USER_BIND_ID","{{%public_sea_contact_follow_user}}","follow_user_id");
	    $this->addColumn('{{%public_sea_claim}}', 'is_claim', 'tinyint(1) DEFAULT 1 COMMENT \'是否算作认领次数\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201228_084348_change_table_public_sea_customer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201228_084348_change_table_public_sea_customer cannot be reverted.\n";

        return false;
    }
    */
}
