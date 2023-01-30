<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%admin_config}}`.
 *
 */
class m210105_062616_create_admin_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%admin_config}}', [
            'group'=>$this->string(255)->comment('配置类型')->defaultValue('')->notNull(),
            'key'=>$this->char(180)->comment('字段名称')->defaultValue('')->notNull(),
            'value'=>$this->text()->comment('字段内容')->notNull(),
            'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
            'update_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('修改时间'),
        ]);

        // creates index for column `key`
        $this->createIndex(
            '{{%idx-key_idx}}',
            '{{%admin_config}}',
            'key'
        );

        $sql = <<<SQL
            SET FOREIGN_KEY_CHECKS=0;
            ALTER TABLE {{%admin_config}} MODIFY COLUMN `update_time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT '修改时间' AFTER `create_time`;
            
            SET FOREIGN_KEY_CHECKS=1;
SQL;

        $this->execute($sql);

        $this->batchInsert('{{%admin_config}}', ['group', 'key', 'value'], [
            ['web', 'web_tech_img',''],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops index for column `key`
        $this->dropIndex(
            '{{%idx-key_idx}}',
            '{{%admin_config}}'
        );

        $this->dropTable('{{%admin_config}}');
    }
}
