<?php

use yii\db\Migration;

/**
 * Class m191205_070926_change_table_message_sign
 */
class m191205_070926_change_table_message_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%message_sign}}', 'apply_time', 'timestamp NULL DEFAULT NULL COMMENT \'申请时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191205_070926_change_table_message_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191205_070926_change_table_message_sign cannot be reverted.\n";

        return false;
    }
    */
}
