<?php

use yii\db\Migration;

/**
 * Class m200324_011631_change_table_awards_activity
 */
class m200324_011631_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%awards_activity}}', 'agent_id', 'int(11) unsigned DEFAULT 0 COMMENT \'应用id\' AFTER `corp_id`');
		$this->addColumn('{{%awards_activity}}', 'config_id', 'varchar(64) DEFAULT \'\' COMMENT \'联系方式的配置id\' AFTER `agent_id`');
		$this->addColumn('{{%awards_activity}}', 'qr_code', 'varchar(255) DEFAULT \'\' COMMENT \'联系二维码的URL\' AFTER `config_id`');
		$this->addColumn('{{%awards_activity}}', 'state', 'varchar(64) DEFAULT \'\' COMMENT \'企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值\' AFTER `qr_code`');
		$this->addColumn('{{%awards_activity}}', 'welcome', 'text COMMENT \'欢迎语\' AFTER `state`');
		$this->addColumn('{{%awards_activity}}', 'user_key', 'text COMMENT \'引流成员\' AFTER `welcome`');
		$this->addColumn('{{%awards_activity}}', 'user', 'text COMMENT \'用户userID列表\' AFTER `user_key`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200324_011631_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_011631_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
