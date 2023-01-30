<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dialout_bind_work_user}}`.
 */
class m210219_095842_create_dialout_bind_work_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dialout_bind_work_user}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id' => $this->integer(11)->unsigned()->comment('授权的企业ID'),
            'user_id' => $this->integer(11)->unsigned()->comment('员工id'),
            'exten' => $this->integer(11)->unsigned()->comment('坐席id'),
            'status' => $this->integer(11)->unsigned()->comment('状态；1，启用；2禁用'),
            'create_time' => $this->timestamp()->defaultValue('2000-01-01 00:00:00')->comment('创建日期'),
        ]);
        $this->addCommentOnTable('{{%dialout_bind_work_user}}', '外呼坐席和员工绑定表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%dialout_bind_work_user}}');
    }
}
