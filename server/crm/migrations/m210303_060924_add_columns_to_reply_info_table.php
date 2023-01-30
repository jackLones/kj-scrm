<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%reply_info}}`.
 */
class m210303_060924_add_columns_to_reply_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%reply_info}}", "appid", $this->string(255)->defaultValue('')->comment('小程序的appid'));
        $this->addColumn("{{%reply_info}}", "pagepath", $this->string(255)->defaultValue('')->comment('小程序的页面路径'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210303_060924_add_columns_to_reply_info_table cannot be reverted.\n";

        return false;
    }
}
