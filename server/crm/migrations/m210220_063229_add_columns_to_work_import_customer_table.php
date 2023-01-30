<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_import_customer}}`.
 */
class m210220_063229_add_columns_to_work_import_customer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_import_customer}}', 'tag_ids', $this->text()->comment('标签id')->defaultValue(''));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_import_customer}}', 'tag_ids');
    }
}
