<?php

use yii\db\Migration;

/**
 * Class m200710_013856_change_table_user
 */
class m200710_013856_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%user}}', 'application_status', 'tinyint(1) DEFAULT 0 COMMENT \'客户资料状态：1已提交未审核，2审核通过，3审核失败\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200710_013856_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200710_013856_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
