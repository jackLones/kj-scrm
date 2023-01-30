<?php

use yii\db\Migration;

/**
 * Class m210401_022514_change_shop_customer_table_change_index
 */
class m210401_022514_change_shop_customer_table_change_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropIndex('KEY_SHOP_CUSTOMER_CORP_ID','{{%shop_customer}}' );
        $this->dropIndex('KEY_SHOP_CUSTOMER_IS_DEL', '{{%shop_customer}}');
        $this->dropIndex('KEY_SHOP_CUSTOMER_PHONE', '{{%shop_customer}}');

        $this->createIndex(
            'KEY_SHOPC_COMPOSITE_INDEX',
            '{{%shop_customer}}',
            ['is_del','corp_id','phone']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropIndex('KEY_SHOPC_COMPOSITE_INDEX', '{{%shop_customer}}');

        $this->createIndex('KEY_SHOP_CUSTOMER_CORP_ID', '{{%shop_customer}}', 'corp_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_PHONE', '{{%shop_customer}}', 'phone');
        $this->createIndex('KEY_SHOP_CUSTOMER_IS_DEL', '{{%shop_customer}}', 'is_del');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210401_022513_change_shop_customer_table_change_index cannot be reverted.\n";

        return false;
    }
    */
}
