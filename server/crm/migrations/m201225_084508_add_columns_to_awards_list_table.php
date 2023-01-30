<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%awards_list}}`.
 */
class m201225_084508_add_columns_to_awards_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%awards_list}}', 'success_tags', $this->char(255)->comment('完成后打上指定标签')->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%awards_list}}', 'success_tags');
    }
}
