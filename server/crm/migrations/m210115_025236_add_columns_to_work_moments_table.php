<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_moments}}`.
 */
class m210115_025236_add_columns_to_work_moments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_moments}}', 'external_status', $this->tinyInteger(1)->unsigned()->notNull()->comment('朋友圈可见范围 1 全部  2指定成员')->defaultValue(1));
        $this->addColumn('{{%work_moments}}', 'external_userid', $this->text()->comment('是否同步官方朋友圈 0否 1是'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210115_025236_add_columns_to_work_moments_table cannot be reverted.\n";

        return false;
    }
}
