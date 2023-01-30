<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%red_pack}}`.
 */
class m201225_090704_add_columns_to_red_pack_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%red_pack}}', 'success_tags', $this->char(255)->comment('完成后打上指定标签')->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%red_pack}}', 'success_tags');
    }
}
