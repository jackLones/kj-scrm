<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspection_remind}}`.
 */
class m210203_060052_create_inspection_remind_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspection_remind}}', [
            'id'          => $this->primaryKey(11)->unsigned(),
            'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID')->defaultValue(0),
            'user_id'     => $this->integer(11)->unsigned()->comment('质检人id')->defaultValue(0),
            'report_id'   => $this->text()->comment('汇报对象id 用逗号隔开'),
            'report_name' => $this->text()->comment('汇报对象名称 用逗号隔开'),
            'report_json' => $this->text()->comment('汇报对象json'),
            'quality_id'  => $this->text()->comment('质检对象id 用逗号隔开'),
            'quality_json'=> $this->text()->comment('质检对象json'),
            'agent_id'    => $this->integer(11)->unsigned()->comment('应用id')->defaultValue(0),
            'is_cycle'    => $this->tinyInteger(1)->unsigned()->comment('周期 0每天 1每周')->defaultValue(0),
            'status'      => $this->tinyInteger(1)->unsigned()->comment('推送是否开启 0关闭 1开启')->defaultValue(1),
            'is_delete'   => $this->tinyInteger(1)->unsigned()->comment('是否删除 0否 1是')->defaultValue(0),
            'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'质检提醒表\'');
        $this->addCommentOnTable('{{%inspection_remind}}', '质检提醒表');

        $this->createIndex(
            '{{%idx-inspection_remind-corp_id}}',
            '{{%inspection_remind}}',
            'corp_id'
        );
        $this->createIndex(
            '{{%idx-inspection_remind-user_id}}',
            '{{%inspection_remind}}',
            'user_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-inspection_remind-corp_id}}',
            '{{%inspection_remind}}'
        );
        $this->dropIndex(
            '{{%idx-inspection_remind-user_id}}',
            '{{%inspection_remind}}'
        );

        $this->dropTable('{{%inspection_remind}}');
    }
}
