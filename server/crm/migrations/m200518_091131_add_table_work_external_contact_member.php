<?php

use yii\db\Migration;

/**
 * Class m200518_091131_add_table_work_external_contact_member
 */
class m200518_091131_add_table_work_external_contact_member extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%work_external_contact_member}}', [
		    'id'              => $this->primaryKey(11)->unsigned(),
		    'external_userid' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
		    'sign_id'         => $this->integer(11)->unsigned()->comment('店铺ID'),
		    'member_id'       => $this->integer(11)->unsigned()->comment('会员id'),
		    'uc_id'           => $this->integer(11)->unsigned()->comment('用户id'),
		    'create_time'     => $this->timestamp()->comment('创建时间'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业外部联系人与第三方关联表\'');
	    $this->addForeignKey('KEY_EXTERNAL_USERID', '{{%work_external_contact_member}}', 'external_userid', '{{%work_external_contact}}', 'id');
	    $this->addForeignKey('KEY_SIGN_ID', '{{%work_external_contact_member}}', 'sign_id', '{{%application_sign}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200518_091131_add_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200518_091131_add_table_work_external_contact_member cannot be reverted.\n";

        return false;
    }
    */
}
