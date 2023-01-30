<?php

use yii\db\Migration;

/**
 * Class m200226_024949_change_table_sub_user
 */
class m200226_024949_change_table_sub_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%sub_user}}', 'access_token_expire', 'int(11) unsigned DEFAULT NULL COMMENT \'对接验证字符串失效时间戳\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200226_024949_change_table_sub_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200226_024949_change_table_sub_user cannot be reverted.\n";

        return false;
    }
    */
}
