<?php

use yii\db\Migration;

/**
 * Class m191212_125756_change_table_message_order
 */
class m191212_125756_change_table_message_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%message_order}}', 'goods_describe', 'varchar(128)  DEFAULT \'\' COMMENT \'产品描述\'');
	    $this->alterColumn('{{%message_order}}', 'paytime', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'支付时间\'');
	    $this->alterColumn('{{%message_order}}', 'add_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'添加时间\'');
	    $this->addColumn('{{%message_order}}', 'extrainfo', 'varchar(256)  DEFAULT \'\' COMMENT \'额外信息\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_125756_change_table_message_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_125756_change_table_message_order cannot be reverted.\n";

        return false;
    }
    */
}
