<?php

use yii\db\Migration;

/**
 * Class m191203_075940_change_table_fans_time_line
 */
class m191203_075940_change_table_fans_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fans_time_line}}', 'source', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'来源 0粉丝给公众号发消息 1关注回复粉丝消息 2扫描渠道二维码回复粉丝消息\'');
	    $this->addColumn('{{%fans_time_line}}', 'remark', 'char(64) DEFAULT NULL COMMENT \'记录二维码名称/关键字/标签名称等备注\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191203_075940_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191203_075940_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }
    */
}
