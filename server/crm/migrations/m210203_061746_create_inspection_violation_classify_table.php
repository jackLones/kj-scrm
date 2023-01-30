<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%inspection_violation_classify}}`.
 */
class m210203_061746_create_inspection_violation_classify_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%inspection_violation_classify}}', [
            'id'          => $this->primaryKey(11)->unsigned(),
            'corp_id'     => $this->integer(11)->unsigned()->comment('企业ID')->defaultValue(0),
            'content'     => $this->char(20)->comment('内容')->defaultValue(''),
            'is_delete'   => $this->tinyInteger(1)->unsigned()->comment('是否删除 0否 1是')->defaultValue(0),
            'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'质检违规分类表\'');
        $this->addCommentOnTable('{{%inspection_violation_classify}}', '质检违规分类表');

        $this->createIndex(
            '{{%idx-inspection_violation_classify-corp_id}}',
            '{{%inspection_violation_classify}}',
            'corp_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%idx-inspection_violation_classify-corp_id}}',
            '{{%inspection_violation_classify}}'
        );

        $this->dropTable('{{%inspection_violation_classify}}');
    }
}
