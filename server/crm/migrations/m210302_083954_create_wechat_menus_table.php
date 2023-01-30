<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wechat_menus}}`.
 */
class m210302_083954_create_wechat_menus_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%wechat_menus}}', [
            'id' => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'appid' => $this->string(32)->notNull()->comment('微信公众号appid')->defaultValue(''),
            'menu_name' => $this->string(100)->notNull()->comment('菜单名称')->defaultValue(''),
            'menu' => $this->text()->notNull()->comment('微信公众号菜单')->defaultValue(''),
            'type' => $this->tinyInteger(1)->unsigned()->notNull()->comment('菜单类型，1：普通菜单、2：个性化菜单')->defaultValue(1),
            'matchrule' => $this->text()->notNull()->comment('个性化菜单匹配规则')->defaultValue(''),
            'menuid' => $this->string()->notNull()->comment('个性化菜单微信返回ID')->defaultValue(''),
            'create_time' => $this->datetime()->defaultValue(NULL),
            'update_time' => $this->datetime()->defaultExpression('NULL ON UPDATE CURRENT_TIMESTAMP'),
        ]);
        $this->addCommentOnTable('{{%wechat_menus}}', '公众号菜单');
        $this->createIndex(
            '{{%appid_idx}}',
            '{{%wechat_menus}}',
            'appid'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%wechat_menus}}');
    }
}
