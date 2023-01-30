<?php

use yii\db\Migration;

/**
 * Class m200426_074847_change_table_sub_user_authority
 */
class m200426_074847_change_table_sub_user_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%sub_user_authority}}", "is_mini", "tinyint(1) DEFAULT 0 COMMENT '0公众号1小程序'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200426_074847_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200426_074847_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }
    */
}
