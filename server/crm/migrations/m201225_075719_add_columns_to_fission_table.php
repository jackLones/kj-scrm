<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%fission}}`.
 */
class m201225_075719_add_columns_to_fission_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%fission}}', 'success_tags', $this->char(255)->comment('完成后打上指定标签')->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%fission}}', 'success_tags');
    }
}
