<?php

use yii\db\Migration;

/**
 * Class m200225_024021_change_table_attachment
 */
class m200225_024021_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%attachment}}", "author", "char(16) DEFAULT NULL COMMENT '作者'");
	    $this->addColumn("{{%attachment}}", "show_cover_pic", "tinyint(1) unsigned DEFAULT '0' COMMENT '是否显示封面 1是0否'");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200225_024021_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200225_024021_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
