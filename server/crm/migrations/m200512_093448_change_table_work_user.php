<?php

use yii\db\Migration;

/**
 * Class m200512_093448_change_table_work_user
 */
class m200512_093448_change_table_work_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_user}}', 'is_external', 'tinyint(1) NOT NULL DEFAULT 0 COMMENT \'是否具有外部联系人权限 1有 0没有\' AFTER `is_del`');
	    $this->createIndex('KEY_WORK_USER_IS_EXTERNAL', '{{%work_user}}', 'is_external');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200512_093448_change_table_work_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200512_093448_change_table_work_user cannot be reverted.\n";

        return false;
    }
    */
}
