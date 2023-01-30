<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_rfm_default}}`.
 */
class m210121_034756_create_shop_customer_rfm_default_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_rfm_default}}', [
            'id'          =>  $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'frequency'   =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('频率0低1⾼'),
            'recency'     =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('近度0低1⾼'),
            'monetary'    =>  $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('额度0低1⾼'),
            'default_name'=>  $this->string(100)->notNull()->defaultValue('')->comment('默认名称'),
            'type'        =>  $this->tinyInteger(1)->notNull()->defaultValue('0')->comment('消费数据:1开启 0未开启'),
            'add_time'    =>  $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_RFM_DEFAULT_TYPE', '{{%shop_customer_rfm_default}}', 'type');
        $this->addCommentOnTable('{{%shop_customer_rfm_default}}', '顾客RFM默认等级表');
        $this->batchInsert('{{%shop_customer_rfm_default}}', ['recency','frequency', 'monetary','default_name','type'], [
            [1,1,1,'重要价值',1],
            [1,0,1,'重要发展',1],
            [0,1,1,'重要保持',1],
            [0,0,1,'重要挽留',1],
            [1,1,0,'一般价值',1],
            [1,0,0,'一般发展',1],
            [0,1,0,'一般保持',1],
            [0,0,0,'一般挽留',1],
            //以下是消费数据未开启时默认数据
            [1,1,0,'高价值',0],
            [1,0,0,'高维护',0],
            [0,1,0,'一般保持',0],
            [0,0,0,'一般挽留',0]
        ]);
      }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%shop_customer_rfm_default}}');
        $this->dropTable('{{%shop_customer_rfm_default}}');
        return false;
    }
}
