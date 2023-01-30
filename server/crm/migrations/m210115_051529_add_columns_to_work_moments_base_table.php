<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_moments_base}}`.
 */
class m210115_051529_add_columns_to_work_moments_base_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_moments_base}}', 'synchro_moment_id', $this->char(34)->unsigned()->comment('同步企业微信朋友圈id'));
        $this->addColumn('{{%work_moments_base}}', 'visible_type', $this->tinyInteger(1)->unsigned()->comment('可见范围类型。0：部分可见 1：公开')->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210115_051529_add_columns_to_work_moments_base_table cannot be reverted.\n";

        return false;
    }
}
