<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shop_customer_level_setting}}`.
 */
class m210121_030031_create_shop_customer_level_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shop_customer_level_setting}}', [
            'id'         => $this->primaryKey(11)->unsigned()->notNull()->defaultExpression('AUTO_INCREMENT'),
            'corp_id'    => $this->integer(11)->unsigned()->notNUll()->defaultValue(0)->comment('授权的企业ID'),
            'title'      => $this->string(100)->defaultValue('')->notNull()->comment('等级名称'),
            'desc'       => $this->text()->notNull()->defaultValue('')->comment('等级描述'),
            'weight'     => $this->smallInteger(5)->unsigned()->notNUll()->defaultValue(0)->comment('权重（值越大等级越高）'),
            'color'      => $this->string(10)->notNull()->defaultValue('')->comment('等级颜⾊值'),
            'sort'       => $this->smallInteger(5)->defaultValue(100)->notNull()->unsigned()->comment('排序'),
            'add_time'   => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('入库时间'),
            'update_time'=> $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')->comment('更新时间')],
            'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->createIndex('KEY_SHOP_CUSTOMER_LEVEL_SETTING_CORP_ID', '{{%shop_customer_level_setting}}', 'corp_id');
        $this->createIndex('KEY_SHOP_CUSTOMER_LEVEL_SETTING_WEIGHT',  '{{%shop_customer_level_setting}}', 'weight');
        $this->createIndex('KEY_SHOP_CUSTOMER_LEVEL_SETTING_SORT',    '{{%shop_customer_level_setting}}', 'sort');
        $this->addCommentOnTable('{{%shop_customer_level_setting}}', '顾客等级表');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%shop_customer_level_setting}}');
        return false;
    }
}
