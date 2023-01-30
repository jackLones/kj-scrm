<?php

use yii\db\Migration;

/**
 * Class m190911_095407_change_table_fans_behavior
 */
class m190911_095407_change_table_fans_behavior extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn("{{%fans_behavior}}", "author_id", "int(11) unsigned NOT NULL COMMENT '公众号ID' AFTER `id`");

    	$this->createIndex("KEY_FANS_BEHAVIOR_AUTHORID", "{{%fans_behavior}}", "author_id");

    	$this->addForeignKey("KEY_FANS_BEHAVIOR_AUTHORID", "{{%fans_behavior}}", "author_id", "{{%wx_authorize}}", "author_id");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190911_095407_change_table_fans_behavior cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190911_095407_change_table_fans_behavior cannot be reverted.\n";

        return false;
    }
    */
}
