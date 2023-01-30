<?php

use yii\db\Migration;

/**
 * Class m200529_101138_change_table_youzan_order
 */
class m200529_101138_change_table_youzan_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%youzan_order}}', 'express_type', 'int(3) NOT NULL DEFAULT 0 COMMENT \'配送方式 0:快递发货; 1:到店自提; 2:同城配送; 9:无需发货 \' AFTER `status` ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200529_101138_change_table_youzan_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200529_101138_change_table_youzan_order cannot be reverted.\n";

        return false;
    }
    */
}
