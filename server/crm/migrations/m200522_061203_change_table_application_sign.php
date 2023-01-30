<?php

use yii\db\Migration;

/**
 * Class m200522_061203_change_table_application_sign
 */
class m200522_061203_change_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%application_sign}}', 'is_bind', 'tinyint(1) unsigned DEFAULT \'0\' COMMENT \'是否绑定 1已绑定 0未绑定 \' AFTER `username` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200522_061203_change_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200522_061203_change_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
