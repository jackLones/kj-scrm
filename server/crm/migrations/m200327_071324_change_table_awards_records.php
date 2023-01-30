<?php

use yii\db\Migration;

/**
 * Class m200327_071324_change_table_awards_records
 */
class m200327_071324_change_table_awards_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->renameColumn('{{%awards_records}}', 'user_id', 'openid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_071324_change_table_awards_records cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_071324_change_table_awards_records cannot be reverted.\n";

        return false;
    }
    */
}
