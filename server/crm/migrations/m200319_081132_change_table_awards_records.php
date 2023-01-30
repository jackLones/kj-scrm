<?php

use yii\db\Migration;

/**
 * Class m200319_081132_change_table_awards_records
 */
class m200319_081132_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_records}}', 'aid', 'int(11) unsigned DEFAULT 0 COMMENT \'奖品id 关联awards_list表\' AFTER `award_id`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200319_081132_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200319_081132_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
