<?php

use yii\db\Migration;

/**
 * Class m200402_022114_change_table_package
 */
class m200402_022114_change_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package}}', 'wechat_num', 'int(11) UNSIGNED COMMENT \'企业微信数量\' AFTER `price`');
	    $this->addColumn('{{%package}}', 'account_num', 'int(11) UNSIGNED COMMENT \'公众号数量\' AFTER `wechat_num`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200402_022114_change_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200402_022114_change_table_package cannot be reverted.\n";

        return false;
    }
    */
}
