<?php

use yii\db\Migration;

/**
 * Class m191218_113854_change_table_interact_reply_detail
 */
class m191218_113854_change_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%interact_reply_detail}}', 'auto_id', 'int(11) unsigned DEFAULT NULL COMMENT \'关联pig_auto_reply表的id\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_113854_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_113854_change_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
