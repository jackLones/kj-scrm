<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%work_external_contact}}`.
 */
class m210129_032451_add_columns_to_work_external_contact_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%work_external_contact}}', 'update_time', $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%work_external_contact}}', 'update_time');
    }
}
