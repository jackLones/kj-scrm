<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_guide_data_series}}`.
 */
class m210222_033646_create_shop_guide_data_series_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_guide_data_series}}', [
            'id'              => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'         => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'store_id'        => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('门店id'),
            'guide_id'        => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('导购id'),
            'monetary'        => $this->decimal(10, 2)->defaultValue('0.00')->notNUll()->unsigned()->comment('日销售额'),
            'add_user_number' => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('日拉新数'),
            'add_day'         => $this->string(10)->notNUll()->defaultValue('')->comment('日期天'),
            'add_month'       => $this->string(10)->notNUll()->defaultValue('')->comment('日期月'),
            'add_year'        => $this->string(10)->notNUll()->defaultValue('')->comment('日期年'),
            'add_time'        => $this->timestamp()->notNUll()->defaultExpression('CURRENT_TIMESTAMP')->comment('操作时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_CORP_ID', '{{%shop_guide_data_series}}', 'corp_id');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_STORE_ID', '{{%shop_guide_data_series}}', 'store_id');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_GUIDE_ID', '{{%shop_guide_data_series}}', 'guide_id');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_ADD_DAY', '{{%shop_guide_data_series}}', 'add_day');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_ADD_MONTH', '{{%shop_guide_data_series}}', 'add_month');
        $this->createIndex('KEY_SHOP_GUIDE_DATA_SERIES_ADD_YEAR', '{{%shop_guide_data_series}}', 'add_year');
        $this->addCommentOnTable('{{%shop_guide_data_series}}', '日数据表-导购');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_guide_data_series}}');
    }
}
