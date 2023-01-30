<?php

use yii\db\Migration;

/**
 * Class m210120_071605_change_table_work_sop
 */
class m210120_071605_change_table_work_sop extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_sop}}', 'is_chat', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否群SOP规则1是0否\' after `corp_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210120_071605_change_table_work_sop cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_071605_change_table_work_sop cannot be reverted.\n";

        return false;
    }
    */
}
