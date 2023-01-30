<?php

use yii\db\Migration;

/**
 * Class m200527_033225_change_table_taobao_order
 */
class m200527_033225_change_table_taobao_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%taobao_order}}', 'status', 'tinyint(1) unsigned DEFAULT \'0\'  COMMENT \'是否显示：0：不显示、1：显示\' AFTER `remark`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200527_033225_change_table_taobao_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200527_033225_change_table_taobao_order cannot be reverted.\n";

        return false;
    }
    */
}
