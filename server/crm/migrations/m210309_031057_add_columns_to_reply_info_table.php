<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%reply_info}}`.
 */
class m210309_031057_add_columns_to_reply_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%reply_info}}', 'menu_keyword_id', $this->integer(11)->unsigned()->comment('菜单关键字ID'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%reply_info}}', 'menu_keyword_id');
    }
}
