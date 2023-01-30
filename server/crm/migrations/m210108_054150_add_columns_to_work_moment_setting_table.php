<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_moment_setting}}`.
 */
class m210108_054150_add_columns_to_work_moment_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_moment_setting}}', 'is_synchro', $this->tinyInteger(1)->unsigned()->notNull()->comment('是否同步官方朋友圈 0否 1是')->defaultValue(1));
        $this->addColumn('{{%work_moment_setting}}', 'is_synchro_all', $this->tinyInteger(1)->unsigned()->notNull()->comment('是否同步之前全部朋友圈数据 0否 1是')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_moment_setting}}', 'is_synchro');
    }
}
