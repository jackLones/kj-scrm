<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%follow}}`.
 */
class m210115_015459_add_columns_to_follow_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%follow}}', 'lose_one', $this->tinyInteger(1)->comment('是否输单0否1是')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%follow}}', 'lose_one');
    }
}
