<?php

use yii\db\Migration;

/**
 * Class m200623_051248_change_table_work_group_sending
 */
class m200623_051248_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%work_group_sending}}', 'send_type', 'tinyint(1) DEFAULT NULL COMMENT \'1、全部客户 2、按条件筛选客户 3、企业成员 4、全部群聊 5、部分群聊\' AFTER `title`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_051248_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200623_051248_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
