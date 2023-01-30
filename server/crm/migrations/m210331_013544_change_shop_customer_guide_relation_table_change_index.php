<?php

use yii\db\Migration;

/**
 * Class m210331_013543_change_shop_customer_guide_relation_table_change_index
 */
class m210331_013544_change_shop_customer_guide_relation_table_change_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('KEY_shop_customer_guide_relation_CORP_ID','{{%shop_customer_guide_relation}}' );
        $this->dropIndex('KEY_shop_customer_guide_relation_CUS_ID', '{{%shop_customer_guide_relation}}');
        $this->dropIndex('KEY_shop_customer_guide_relation_GUIDE_ID', '{{%shop_customer_guide_relation}}');
        $this->dropIndex('KEY_shop_customer_guide_relation_SOURCE_TYPE', '{{%shop_customer_guide_relation}}');
        $this->dropIndex('KEY_shop_customer_guide_relation_STATUS', '{{%shop_customer_guide_relation}}');

        $this->createIndex(
            'KEY_SCGR_COMPOSITE_INDEX',
            '{{%shop_customer_guide_relation}}',
                  ['status','corp_id','cus_id','guide_id','store_id']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('KEY_SCGR_COMPOSITE_INDEX','{{%shop_customer_guide_relation}}' );
        $this->createIndex('KEY_shop_customer_guide_relation_CORP_ID', '{{%shop_customer_guide_relation}}', 'corp_id');
        $this->createIndex('KEY_shop_customer_guide_relation_CUS_ID', '{{%shop_customer_guide_relation}}', 'cus_id');
        $this->createIndex('KEY_shop_customer_guide_relation_GUIDE_ID', '{{%shop_customer_guide_relation}}', 'guide_id');
        $this->createIndex('KEY_shop_customer_guide_relation_STATUS', '{{%shop_customer_guide_relation}}', 'status');
        $this->createIndex('KEY_shop_customer_guide_relation_SOURCE_TYPE', '{{%shop_customer_guide_relation}}', 'source_type');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210331_013543_change_shop_customer_guide_relation_table_change_index cannot be reverted.\n";

        return false;
    }
    */
}
