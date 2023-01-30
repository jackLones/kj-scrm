<?php

use yii\db\Migration;

/**
 * Class m200707_055837_add_table_work_external_contact_follow_statistic
 */
class m200707_055837_add_table_work_external_contact_follow_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%work_external_contact_follow_statistic}}', [
		    'id'              => $this->primaryKey(11)->unsigned(),
		    'corp_id'         => $this->integer(11)->unsigned()->comment('授权的企业ID'),
		    'external_userid' => $this->integer(11)->unsigned()->comment('外部联系人ID'),
		    'user_id'         => $this->integer(11)->unsigned()->comment('成员ID'),
		    'follow_id'       => $this->integer(11)->unsigned()->comment('跟进状态ID'),
		    'data_time'       => $this->char(64)->comment('统计时间'),
		    'type'            => $this->tinyInteger(1)->comment('类型1日2周3月'),
		    'create_time'     => $this->timestamp()->defaultValue(NULL)->comment('创建时间'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'客户跟进统计\'');

	    $this->createIndex('KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_TYPE', '{{%work_external_contact_follow_statistic}}', 'type');
	    $this->addForeignKey('KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_CORP_ID', '{{%work_external_contact_follow_statistic}}', 'corp_id', '{{%work_corp}}', 'id');
	    $this->addForeignKey('KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_USER_ID', '{{%work_external_contact_follow_statistic}}', 'user_id', '{{%work_user}}', 'id');
	    $this->addForeignKey('KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_FOLLOW_ID', '{{%work_external_contact_follow_statistic}}', 'follow_id', '{{%follow}}', 'id');
	    $this->addForeignKey('KEY_EXTERNAL_CONTACT_FOLLOW_STATISTIC_EXTERNAL_USERID', '{{%work_external_contact_follow_statistic}}', 'external_userid', '{{%work_external_contact}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200707_055837_add_table_work_external_contact_follow_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200707_055837_add_table_work_external_contact_follow_statistic cannot be reverted.\n";

        return false;
    }
    */
}
