<?php

use yii\db\Migration;

/**
 * Class m200211_071353_change_table_attachment
 */
class m200211_071353_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment}}', 'is_temp', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否是临时\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200211_071353_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200211_071353_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
