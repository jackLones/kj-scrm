<?php

use yii\db\Migration;

/**
 * Class m210413_014146_change_table_work_tag
 */
class m210413_014146_change_table_work_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->dropForeignKey("KEY_WORK_TAG_CORPID", "{{%work_tag}}");
	    $this->alterColumn('{{%work_tag}}', 'type', 'tinyint(1) DEFAULT \'0\' COMMENT \'类型0外部联系人 1员工 2客户群 3内容标签\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210413_014146_change_table_work_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210413_014146_change_table_work_tag cannot be reverted.\n";

        return false;
    }
    */
}
