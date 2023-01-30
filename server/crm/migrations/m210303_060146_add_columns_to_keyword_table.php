<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%keyword}}`.
 */
class m210303_060146_add_columns_to_keyword_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%keyword}}", "equal_keyword", $this->string(255)->defaultValue('')->comment('全匹配关键词'));
        $this->addColumn("{{%keyword}}", "contain_keyword", $this->string(255)->defaultValue('')->comment('半匹配关键词'));
        $this->addColumn("{{%keyword}}", "is_del", $this->tinyInteger(1)->defaultValue(0)->comment("是否删除 0否 1是"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210303_060146_add_columns_to_keyword_table cannot be reverted.\n";

        return false;
    }
}
