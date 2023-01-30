<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%radar_link}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%associat}}`
 */
class m201113_085307_create_radar_link_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%radar_link}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'associat_type' => $this->tinyInteger(1)->unsigned()->notNull()->comment('关联类型，0：内容引擎（附件表）、1：渠道活码、2：欢迎语、3：群欢迎语')->defaultValue(0),
            'associat_id' => $this->integer(11)->unsigned()->comment('关联id')->defaultValue('0'),
            'associat_param' => $this->string(255)->comment('关联参数')->defaultValue(NULL),
            'title' => $this->char(64)->comment('标题')->defaultValue(NULL),
            'dynamic_notification' => $this->tinyInteger(1)->unsigned()->notNull()->comment('是否启用动态通知，0：不启用、1：启用')->defaultValue(0),
            'radar_tag_open' => $this->tinyInteger(1)->unsigned()->notNull()->comment('是否启用标签，0：不启用、1：启用')->defaultValue(0),
            'tag_ids' => $this->text()->comment('给客户打的标签'),
            'open_times' => $this->integer(11)->unsigned()->notNull()->comment('打开次数')->defaultValue('0'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间'),
            'status' => $this->tinyInteger(1)->unsigned()->notNull()->comment('状态，1：可用、0：不可用')->defaultValue(0),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        // creates index for column `associat_id`
        $this->createIndex(
            '{{%idx-radar_link-associat_id}}',
            '{{%radar_link}}',
            'associat_id'
        );

        $this->addCommentOnTable('{{%radar_link}}', '雷达链接表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        // drops index for column `associat_id`
        $this->dropIndex(
            '{{%idx-radar_link-associat_id}}',
            '{{%radar_link}}'
        );

        $this->dropTable('{{%radar_link}}');
    }
}
