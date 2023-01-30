<?php

use yii\db\Migration;

/**
 * Class m200409_013722_add_table_custom_field
 */
class m200409_013722_add_table_custom_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%custom_field}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL COMMENT '字段名称  ps:英文名称用于input下的name',
  `title` varchar(64) NOT NULL COMMENT '字段标题 ps:中文标题用于告诉用户字段用处',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT ' 字段备注',
  `type` tinyint(1) NOT NULL COMMENT '字段类型 （1文本 2单选 3多选 4日期 5手机号 6邮箱 7区域）',
  `is_define` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否商户自定义 1是0否',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '自定义商户的uid',
  `updatetime` int(10) NOT NULL COMMENT ' 修改时间',
  `createtime` int(10) NOT NULL COMMENT ' 创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0关闭，1开启，2删除',
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOM_FIELD_UID` (`uid`),
  KEY `KEY_CUSTOM_FIELD_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户高级属性字段表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200409_013722_add_table_custom_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200409_013722_add_table_custom_field cannot be reverted.\n";

        return false;
    }
    */
}
