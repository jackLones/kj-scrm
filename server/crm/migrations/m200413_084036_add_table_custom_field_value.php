<?php

use yii\db\Migration;

/**
 * Class m200413_084036_add_table_custom_field_value
 */
class m200413_084036_add_table_custom_field_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%custom_field_value}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型：1客户2粉丝',
  `cid` int(10) NOT NULL COMMENT '用户id',
  `fieldid` int(10) NOT NULL COMMENT '属性字段表id',
  `value` varchar(500) NOT NULL DEFAULT '' COMMENT '用户属性值',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOM_FIELD_UID` (`uid`),
  KEY `KEY_CUSTOM_FIELD_CID` (`cid`),
  KEY `KEY_CUSTOM_FIELD_FIELDID` (`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户属性详情表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200413_084036_add_table_custom_field_value cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200413_084036_add_table_custom_field_value cannot be reverted.\n";

        return false;
    }
    */
}
