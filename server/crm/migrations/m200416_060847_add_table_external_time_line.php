<?php

use yii\db\Migration;

/**
 * Class m200416_060847_add_table_external_time_line
 */
class m200416_060847_add_table_external_time_line extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%external_time_line}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `external_id` int(10) NOT NULL COMMENT '外部联系人ID',
  `sub_id` int(10) NOT NULL DEFAULT '0' COMMENT '子账户ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '成员ID',
  `event` char(32) DEFAULT NULL COMMENT '行为，类别见model',
  `event_time` int(10) NOT NULL DEFAULT '0' COMMENT '行为时间',
  `event_id` int(11) unsigned DEFAULT '0' COMMENT '行为事件id',
  `remark` VARCHAR(500) DEFAULT '' COMMENT '行为相关备注',
  PRIMARY KEY (`id`),
  KEY `KEY_EXTERNAL_TIME_LINE_UID` (`uid`),
  KEY `KEY_EXTERNAL_TIME_LINE_EXTERNALID` (`external_id`),
  KEY `KEY_EXTERNAL_TIME_LINE_EVENT` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户行为轨迹表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200416_060847_add_table_external_time_line cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200416_060847_add_table_external_time_line cannot be reverted.\n";

        return false;
    }
    */
}
