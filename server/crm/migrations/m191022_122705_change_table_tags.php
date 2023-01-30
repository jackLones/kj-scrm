<?php

use yii\db\Migration;

/**
 * Class m191022_122705_change_table_tags
 */
class m191022_122705_change_table_tags extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%tags}}', 'will_fans_num', 'int(11) NOT NULL COMMENT \'微信后台标签粉丝数\' AFTER `count` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191022_122705_change_table_tags cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191022_122705_change_table_tags cannot be reverted.\n";

        return false;
    }
    */
}
