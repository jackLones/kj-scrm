<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%radar_link_statistic}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%radar_link}}`
 * - `{{%work_corp}}`
 * - `{{%work_user}}`
 * - `{{%work_external_contact}}`
 * - `{{%work_chat}}`
 */
class m201113_085319_create_radar_link_statistic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%radar_link_statistic}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'radar_link_id' => $this->integer(11)->unsigned()->notNull()->comment('雷达链接ID'),
            'corp_id' => $this->integer(11)->unsigned()->comment('企业ID')->defaultValue(NULL),
            'user_id' => $this->integer(11)->unsigned()->comment('成员ID')->defaultValue(NULL),
            'external_id' => $this->integer(11)->unsigned()->comment('外部联系人ID')->defaultValue(NULL),
            'chat_id' => $this->integer(11)->unsigned()->comment('客户群ID')->defaultValue(NULL),
            'openid' => $this->char(64)->comment('用户openid')->defaultValue(NULL),
            'open_time' => $this->timestamp()->comment('打开时间')->defaultValue(NULL),
            'leave_time' => $this->timestamp()->comment('离开时间')->defaultValue(NULL),
            'clicks' => $this->integer(11)->unsigned()->notNull()->comment('打开次数')->defaultValue(1),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // creates index for column `radar_link_id`
        $this->createIndex(
            '{{%idx-radar_link_statistic-radar_link_id}}',
            '{{%radar_link_statistic}}',
            'radar_link_id'
        );

        // add foreign key for table `{{%radar_link}}`
        $this->addForeignKey(
            '{{%fk-radar_link_statistic-radar_link_id}}',
            '{{%radar_link_statistic}}',
            'radar_link_id',
            '{{%radar_link}}',
            'id',
            'CASCADE'
        );

        // creates index for column `corp_id`
        $this->createIndex(
            '{{%idx-radar_link_statistic-corp_id}}',
            '{{%radar_link_statistic}}',
            'corp_id'
        );

        // add foreign key for table `{{%work_corp}}`
        $this->addForeignKey(
            '{{%fk-radar_link_statistic-corp_id}}',
            '{{%radar_link_statistic}}',
            'corp_id',
            '{{%work_corp}}',
            'id',
            'CASCADE'
        );

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-radar_link_statistic-user_id}}',
            '{{%radar_link_statistic}}',
            'user_id'
        );

        // add foreign key for table `{{%work_user}}`
        $this->addForeignKey(
            '{{%fk-radar_link_statistic-user_id}}',
            '{{%radar_link_statistic}}',
            'user_id',
            '{{%work_user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `external_id`
        $this->createIndex(
            '{{%idx-radar_link_statistic-external_id}}',
            '{{%radar_link_statistic}}',
            'external_id'
        );

        // add foreign key for table `{{%work_external_contact}}`
        $this->addForeignKey(
            '{{%fk-radar_link_statistic-external_id}}',
            '{{%radar_link_statistic}}',
            'external_id',
            '{{%work_external_contact}}',
            'id',
            'CASCADE'
        );

        // creates index for column `chat_id`
        $this->createIndex(
            '{{%idx-radar_link_statistic-chat_id}}',
            '{{%radar_link_statistic}}',
            'chat_id'
        );

        // add foreign key for table `{{%work_chat}}`
        $this->addForeignKey(
            '{{%fk-radar_link_statistic-chat_id}}',
            '{{%radar_link_statistic}}',
            'chat_id',
            '{{%work_chat}}',
            'id',
            'CASCADE'
        );
        $this->addCommentOnTable('{{%radar_link_statistic}}', '雷达链接统计表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%radar_link}}`
        $this->dropForeignKey(
            '{{%fk-radar_link_statistic-radar_link_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops index for column `radar_link_id`
        $this->dropIndex(
            '{{%idx-radar_link_statistic-radar_link_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops foreign key for table `{{%work_corp}}`
        $this->dropForeignKey(
            '{{%fk-radar_link_statistic-corp_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops index for column `corp_id`
        $this->dropIndex(
            '{{%idx-radar_link_statistic-corp_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops foreign key for table `{{%work_user}}`
        $this->dropForeignKey(
            '{{%fk-radar_link_statistic-user_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-radar_link_statistic-user_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops foreign key for table `{{%work_external_contact}}`
        $this->dropForeignKey(
            '{{%fk-radar_link_statistic-external_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops index for column `external_id`
        $this->dropIndex(
            '{{%idx-radar_link_statistic-external_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops foreign key for table `{{%work_chat}}`
        $this->dropForeignKey(
            '{{%fk-radar_link_statistic-chat_id}}',
            '{{%radar_link_statistic}}'
        );

        // drops index for column `chat_id`
        $this->dropIndex(
            '{{%idx-radar_link_statistic-chat_id}}',
            '{{%radar_link_statistic}}'
        );

        $this->dropTable('{{%radar_link_statistic}}');
    }
}
