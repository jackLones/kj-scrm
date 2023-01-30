<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%awards_activity}}`.
 */
class m201225_083631_add_columns_to_awards_activity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%awards_activity}}', 'tags_local', $this->integer(1)->defaultValue(NULL)->comment('标签位置1总2奖品'));
        $this->addColumn('{{%awards_activity}}', 'success_tags', $this->char(255)->comment('完成后打上指定标签')->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%awards_activity}}', 'tags_local');
        $this->dropColumn('{{%awards_activity}}', 'success_tags');
    }
}
