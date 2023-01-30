<?php

use yii\db\Migration;

/**
 * Class m191023_011338_change_table_tags
 */
class m191023_011338_change_table_tags extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->renameColumn('{{%tags}}','will_fans_num','wx_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191023_011338_change_table_tags cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191023_011338_change_table_tags cannot be reverted.\n";

        return false;
    }
    */
}
