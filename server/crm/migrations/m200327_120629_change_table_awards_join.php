<?php

use yii\db\Migration;

/**
 * Class m200327_120629_change_table_awards_join
 */
class m200327_120629_change_table_awards_join extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%awards_join}}', 'last_time', 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' COMMENT \'最后一次抽奖时间\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_120629_change_table_awards_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_120629_change_table_awards_join cannot be reverted.\n";

        return false;
    }
    */
}
