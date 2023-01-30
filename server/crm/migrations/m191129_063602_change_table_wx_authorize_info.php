<?php

use yii\db\Migration;

/**
 * Class m191129_063602_change_table_wx_authorize_info
 */
class m191129_063602_change_table_wx_authorize_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%wx_authorize_info}}', 'last_tag_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'最后一次同步标签时间\' AFTER `create_time`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191129_063602_change_table_wx_authorize_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191129_063602_change_table_wx_authorize_info cannot be reverted.\n";

        return false;
    }
    */
}
