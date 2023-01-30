<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wechat_menus_keyword_relation}}`.
 */
class m210305_103718_create_wechat_menus_keyword_relation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wechat_menus_keyword_relation}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'appid' => $this->string(32)->notNull()->comment('微信公众号appid')->defaultValue(''),
            'menu_id' => $this->integer(11)->unsigned()->notNull()->comment('微信菜单ID')->defaultValue(0),
            'keyword' => $this->string(32)->notNull()->comment('菜单KEY值')->defaultValue(''),
            'create_time' => $this->datetime()->defaultValue(NULL),
            'update_time' => $this->datetime()->defaultExpression('NULL ON UPDATE CURRENT_TIMESTAMP')
        ]);
        $this->addCommentOnTable('{{%wechat_menus_keyword_relation}}', '公众号菜单关键字关联表');
        $this->createIndex(
            '{{%appid_idx}}',
            '{{%wechat_menus_keyword_relation}}',
            ['appid','keyword'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wechat_menus_keyword_relation}}');
    }
}
