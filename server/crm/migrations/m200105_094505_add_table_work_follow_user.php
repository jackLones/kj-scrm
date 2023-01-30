<?php

use yii\db\Migration;

/**
 * Class m200105_094505_add_table_work_follow_user
 */
class m200105_094505_add_table_work_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->createTable('{{%work_follow_user}}', [
    		'id' => $this->primaryKey(11)->unsigned(),
		    'corp_id'       => $this->integer(11)->unsigned()->comment('授权的企业ID'),
		    'user_id'       => $this->integer(11)->unsigned()->comment('成员ID'),
		    'create_time'   => $this->timestamp()->comment('创建时间'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'企业微信配置了客户联系功能的成员表\'');
	    $this->addForeignKey('KEY_WORK_FOLLOW_USER_CORPID', '{{%work_follow_user}}', 'corp_id', '{{%work_corp}}', 'id');
	    $this->addForeignKey('KEY_WORK_FOLLOW_USER_USERID', '{{%work_follow_user}}', 'user_id', '{{%work_user}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200105_094505_add_table_work_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200105_094505_add_table_work_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
