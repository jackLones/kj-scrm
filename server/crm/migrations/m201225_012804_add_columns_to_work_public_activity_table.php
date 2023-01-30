<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_public_activity}}`.
 */
class m201225_012804_add_columns_to_work_public_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_public_activity}}', 'success_tags', $this->char(255)->comment('完成后打上指定标签')->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_public_activity}}', 'success_tags');
    }
}
