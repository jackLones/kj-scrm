<?php

use yii\db\Migration;

/**
 * Class m200422_063015_change_table_user
 */
class m200422_063015_change_table_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%user}}", "limit_corp_num", "int(10) DEFAULT 0 COMMENT '可授权企业微信数量'");
	    $this->addColumn("{{%user}}", "limit_author_num", "int(10) DEFAULT 0 COMMENT '可授权公众号数量'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200422_063015_change_table_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200422_063015_change_table_user cannot be reverted.\n";

        return false;
    }
    */
}
