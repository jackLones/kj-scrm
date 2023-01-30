<?php

use yii\db\Migration;

/**
 * Class m210312_082707_change_dialout_config_table
 */
class m210312_082707_change_dialout_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn("{{%dialout_config}}", "uid", $this->integer()->notNull()->comment('用户ID'));
        $this->addColumn("{{%dialout_config}}", "business_license_url", $this->string(255)->notNull()->comment('营业执照'));
        $this->addColumn("{{%dialout_config}}", "number_attribute", $this->string(800)->notNull()->comment('客户属性'));
        $this->addColumn("{{%dialout_config}}", "customer_words_art", $this->string(2000)->notNull()->comment('客户话术'));
        $this->addColumn("{{%dialout_config}}", "acknowledgement_url", $this->string(255)->null()->comment('承诺函'));
        $this->addColumn("{{%dialout_config}}", "corporate_identity_card_positive_url", $this->string(255)->notNull()->comment('身份证正面照片'));
        $this->addColumn("{{%dialout_config}}", "corporate_identity_card_reverse_url", $this->string(255)->notNull()->comment('身份证反面照片'));
        $this->addColumn("{{%dialout_config}}", "operator_identity_card_positive_url", $this->string(255)->null()->comment('经办人身份证正面照片'));
        $this->addColumn("{{%dialout_config}}", "operator_identity_card_reverse_url", $this->string(255)->null()->comment('经办人身份证反面照片'));
        $this->addColumn("{{%dialout_config}}", "status", $this->tinyInteger()->defaultValue(2)->comment('状态'));
        $this->addColumn("{{%dialout_config}}", "refuse_reason", $this->string(800)->null()->comment('拒绝原因'));
        $this->addColumn("{{%dialout_config}}", "create_time", $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'));

        $this->createIndex(
            '{{%idx-uid_idx}}',
            '{{%dialout_config}}',
            'uid'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-uid_idx}}', '{{%dialout_config}}');

        echo "m210312_082707_change_dialout_config_table cannot be reverted.\n";

        return false;
    }
}
