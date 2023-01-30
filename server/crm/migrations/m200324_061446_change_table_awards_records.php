<?php

use yii\db\Migration;

/**
 * Class m200324_061446_change_table_awards_records
 */
class m200324_061446_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%awards_records}}', 'external_id', 'int(11) unsigned DEFAULT 0 COMMENT \'外部联系人id\' AFTER `user_id`');
		$this->addColumn('{{%awards_records}}', 'config_id', 'varchar(64) DEFAULT \'\' COMMENT \'联系方式的配置id\' AFTER `external_id`');
		$this->addColumn('{{%awards_records}}', 'qr_code', 'varchar(255) DEFAULT \'\' COMMENT \'联系二维码的URL\' AFTER `config_id`');
		$this->addColumn('{{%awards_records}}', 'state', 'varchar(64) DEFAULT \'\' COMMENT \'企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值\' AFTER `qr_code`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200324_061446_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200324_061446_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
