<?php

use yii\db\Migration;

/**
 * Class m200611_020919_change_table_package
 */
class m200611_020919_change_table_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%package}}', 'priceJson', 'text COMMENT \'套餐档位价格\' AFTER `is_trial`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200611_020919_change_table_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200611_020919_change_table_package cannot be reverted.\n";

        return false;
    }
    */
}
