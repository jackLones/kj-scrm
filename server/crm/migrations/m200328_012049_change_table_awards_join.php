<?php

use yii\db\Migration;

/**
 * Class m200328_012049_change_table_awards_join
 */
class m200328_012049_change_table_awards_join extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('{{%awards_join}}', 'avatar', 'varchar(255) DEFAULT \'\' COMMENT \'头像\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200328_012049_change_table_awards_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200328_012049_change_table_awards_join cannot be reverted.\n";

        return false;
    }
    */
}
