<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%work_moment_remind}}`.
 */
class m210108_034856_create_work_moment_remind_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%work_moment_remind}}', [
            'id'          => $this->primaryKey(11)->unsigned(),
            'remind_user_id'     => $this->integer(11)->unsigned()->comment('提醒用户id')->defaultValue(0),
            'user_id'     => $this->integer(11)->unsigned()->comment('成员ID')->defaultValue(0),
            'external_id'     => $this->integer(11)->unsigned()->comment('外部联系人ID')->defaultValue(0),
            'openid'       => $this->char(64)->unsigned()->comment('外部非联系人openid'),
            'related_id' => $this->integer(11)->unsigned()->comment('相关表id')->defaultValue(0),
            'moment_id' => $this->integer(11)->unsigned()->comment('朋友圈id')->defaultValue(0),
            'moment_user_id' => $this->integer(11)->unsigned()->comment('朋友圈所属成员id')->defaultValue(0),
            'status'      => $this->tinyInteger(1)->unsigned()->comment('状态 是否删除 0否 1是')->defaultValue(0),
            'type'      => $this->tinyInteger(1)->unsigned()->comment('类型 1 点赞 2评论')->defaultValue(1),
            'is_show'      => $this->tinyInteger(1)->unsigned()->comment('是否查看 0否 1是')->defaultValue(0),
            'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'朋友圈消息提醒表\'');
        $this->addCommentOnTable('{{%work_moment_remind}}', '朋友圈消息提醒表');

        // creates index for column `corp_id`
        $this->createIndex(
            '{{%idx-work_moment_remind-related_id}}',
            '{{%work_moment_remind}}',
            'related_id'
        );
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-work_moment_remind-related_id}}',
            '{{%work_moment_remind}}'
        );

        $this->dropTable('{{%work_moment_remind}}');
    }
}
