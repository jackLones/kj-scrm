<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspection_violation}}`.
 */
class m210203_061727_create_inspection_violation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspection_violation}}', [
            'id'          => $this->primaryKey(11)->unsigned(),
            'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID')->defaultValue(0),
            'user_id'     => $this->integer(11)->unsigned()->comment('质检人id')->defaultValue(0),
            'quality_id'  => $this->integer(11)->unsigned()->comment('质检对象id')->defaultValue(0),
            'work_msg_audit_info_id'  => $this->integer(11)->unsigned()->comment('会话记录id')->defaultValue(0),
            'to_user_id'  => $this->integer(11)->unsigned()->comment('用户id  群聊时为0')->defaultValue(0),
            'roomid'      => $this->char(64)->comment('群聊id 如果是单聊则为空')->defaultValue(''),
            'content'     => $this->string(255)->comment('批注'),
            'content_classify'   => $this->text()->comment('批注样式格式'),
            'msg_type'    => $this->tinyInteger(1)->unsigned()->comment('会话类型 0 客户 1群聊')->defaultValue(0),
            'status'      => $this->tinyInteger(1)->unsigned()->comment('是否提交 0 未提交 1 已提交')->defaultValue(0),
            'is_delete'   => $this->tinyInteger(1)->unsigned()->comment('是否删除 0否 1是')->defaultValue(0),
            'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'质检信息表\'');
        $this->addCommentOnTable('{{%inspection_violation}}', '质检信息表');

        $this->createIndex(
            '{{%idx-inspection_violation-user_id}}',
            '{{%inspection_violation}}',
            'user_id'
        );
        $this->createIndex(
            '{{%idx-inspection_violation-quality_id}}',
            '{{%inspection_violation}}',
            'quality_id'
        );
        $this->createIndex(
            '{{%idx-inspection_violation-to_user_id}}',
            '{{%inspection_violation}}',
            'to_user_id'
        );
        $this->createIndex(
            '{{%idx-inspection_violation-roomid}}',
            '{{%inspection_violation}}',
            'roomid'
        );
        $this->createIndex(
            '{{%idx-inspection_violation-corp_id}}',
            '{{%inspection_violation}}',
            'corp_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-inspection_violation-user_id}}',
            '{{%inspection_violation}}'
        );
        $this->dropIndex(
            '{{%idx-inspection_violation-quality_id}}',
            '{{%inspection_violation}}'
        );
        $this->dropIndex(
            '{{%idx-inspection_violation-to_user_id}}',
            '{{%inspection_violation}}'
        );
        $this->dropIndex(
            '{{%idx-inspection_violation-roomid}}',
            '{{%inspection_violation}}'
        );
        $this->dropIndex(
            '{{%idx-inspection_violation-corp_id}}',
            '{{%inspection_violation}}'
        );

        $this->dropTable('{{%inspection_violation}}');
    }
}
