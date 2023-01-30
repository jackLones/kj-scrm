<?php

use yii\db\Migration;

/**
 * Class m210112_084839_change_table_work_user
 */
class m210112_084839_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_user}}', 'sop_msg_status', 'tinyint(1) DEFAULT 0 COMMENT \'SOP消息免打扰是否开启1是0否\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_084839_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210112_084839_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
