<?php

use yii\db\Migration;

/**
 * Class m191217_020827_change_table_fans_time_line
 */
class m191217_020827_change_table_fans_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%fans_time_line}}', 'remark', 'text DEFAULT NULL COMMENT \'记录二维码名称/关键字/标签名称等备注\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191217_020827_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191217_020827_change_table_fans_time_line cannot be reverted.\n";

        return false;
    }
    */
}
