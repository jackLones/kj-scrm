<?php

use yii\db\Migration;

/**
 * Class m200229_072828_change_table_sub_user_authority
 */
class m200229_072828_change_table_sub_user_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%sub_user_authority}}', 'type', ' tinyint(2) unsigned DEFAULT NULL COMMENT \'类型1公众号2企业微信3公共模块\' after `authority_ids` ');
	    $this->addColumn('{{%sub_user_authority}}', 'wx_id', ' int unsigned DEFAULT NULL COMMENT \'公众号或企业微信id\' after `authority_ids` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200229_072828_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200229_072828_change_table_sub_user_authority cannot be reverted.\n";

        return false;
    }
    */
}
