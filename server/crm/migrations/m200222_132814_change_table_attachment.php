<?php

use yii\db\Migration;

/**
 * Class m200222_132814_change_table_attachment
 */
class m200222_132814_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn("{{%attachment}}", "is_editor", "tinyint(1) DEFAULT '0' COMMENT '是否编辑器创建 1是0否'");
	    $this->addColumn("{{%attachment}}", "image_text", "text COMMENT '编辑器图文内容' AFTER `content`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200222_132814_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200222_132814_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
