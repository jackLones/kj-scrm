<?php

use yii\db\Migration;

/**
 * Class m200310_075656_change_table_attachment
 */
class m200310_075656_change_table_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%attachment}}', 'sub_id', 'int(11) UNSIGNED NULL DEFAULT 0 COMMENT \'子账户id\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200310_075656_change_table_attachment cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200310_075656_change_table_attachment cannot be reverted.\n";

        return false;
    }
    */
}
