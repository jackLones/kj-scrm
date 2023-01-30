<?php

use yii\db\Migration;

/**
 * Class m200108_063951_change_table_work_tag_group
 */
class m200108_063951_change_table_work_tag_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_tag_group}}', 'type', 'tinyint(1) DEFAULT "0" COMMENT \'类型0 客户管理 1 通讯录\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200108_063951_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200108_063951_change_table_work_tag_group cannot be reverted.\n";

        return false;
    }
    */
}
