<?php

use yii\db\Migration;

/**
 * Class m200409_030455_add_table_custom_field_option
 */
class m200409_030455_add_table_custom_field_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%custom_field_option}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `fieldid` int(10) NOT NULL COMMENT '高级属性字段表id',
  `value` int(10) NOT NULL DEFAULT '0' COMMENT '对应的值',
  `match` varchar(255) NOT NULL DEFAULT '' COMMENT '对应值的选项信息',
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOM_FIELD_FIELDID` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户高级属性字段选项表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200409_030455_add_table_custom_field_option cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200409_030455_add_table_custom_field_option cannot be reverted.\n";

        return false;
    }
    */
}
