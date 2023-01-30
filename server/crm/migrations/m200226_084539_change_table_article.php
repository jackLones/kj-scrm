<?php

use yii\db\Migration;

/**
 * Class m200226_084539_change_table_article
 */
class m200226_084539_change_table_article extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%article}}", "status", "tinyint(1) NOT NULL DEFAULT '1' COMMENT '1可用 0不可用'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200226_084539_change_table_article cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_084539_change_table_article cannot be reverted.\n";

        return false;
    }
    */
}
