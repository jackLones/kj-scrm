<?php

use yii\db\Migration;

/**
 * Class m210118_073943_change_table_awards_activity
 */
class m210118_073943_change_table_awards_activity extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%awards_activity}}', 'is_share_open', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'是否开启分享设置\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210118_073943_change_table_awards_activity cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210118_073943_change_table_awards_activity cannot be reverted.\n";

        return false;
    }
    */
}
