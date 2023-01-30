<?php

use yii\db\Migration;

/**
 * Class m200327_115438_add_table_awards_join
 */
class m200327_115438_add_table_awards_join extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%awards_join}}', 'nick_name', 'varchar(255) DEFAULT "" COMMENT \'昵称\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_115438_add_table_awards_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_115438_add_table_awards_join cannot be reverted.\n";

        return false;
    }
    */
}
