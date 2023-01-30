<?php

use yii\db\Migration;

/**
 * Class m200512_034455_change_table_work_external_contact_follow_user
 */
class m200512_034455_change_table_work_external_contact_follow_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%work_external_contact_follow_user}}', 'tags', 'text COMMENT \'该成员添加此外部联系人所打标签的分组名称（标签功能需要企业微信升级到2.7.5及以上版本）\' AFTER `createtime`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200512_034455_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200512_034455_change_table_work_external_contact_follow_user cannot be reverted.\n";

        return false;
    }
    */
}
