<?php

use yii\db\Migration;

/**
 * Handles dropping columns from table `{{%work_msg_audit}}`.
 */
class m200527_032421_drop_key_version_columns_from_work_msg_audit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%work_msg_audit}}', 'key_version');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%work_msg_audit}}', 'key_version', $this->integer(11)->unsigned()->comment('加密此条消息使用的公钥版本号。Uint32类型'));
    }
}
