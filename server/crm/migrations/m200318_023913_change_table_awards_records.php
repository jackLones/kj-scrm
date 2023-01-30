<?php

use yii\db\Migration;

/**
 * Class m200318_023913_change_table_awards_records
 */
class m200318_023913_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_records}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'状态 0 未核销 1 已核销\' AFTER `award_name`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200318_023913_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200318_023913_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
