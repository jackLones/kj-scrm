<?php

use yii\db\Migration;

/**
 * Class m200107_022442_change_table_message_push_detail
 */
class m200107_022442_change_table_message_push_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%message_push_detail}}', 'status', 'tinyint(1) DEFAULT "0" COMMENT \'状态：0未发送、1已发送、2发送失败、3发送中、4未知\'');
	    $this->createIndex('KEY_MESSAGE_PUSH_DETAIL_STATUS', '{{%message_push_detail}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200107_022442_change_table_message_push_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200107_022442_change_table_message_push_detail cannot be reverted.\n";

        return false;
    }
    */
}
