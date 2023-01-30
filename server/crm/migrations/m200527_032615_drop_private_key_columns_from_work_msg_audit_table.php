<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%work_msg_audit}}`.
 */
class m200527_032615_drop_private_key_columns_from_work_msg_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%work_msg_audit}}', 'private_key');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%work_msg_audit}}', 'private_key', $this->text()->comment('私钥'));
    }
}
