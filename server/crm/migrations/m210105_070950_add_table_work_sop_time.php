<?php

use yii\db\Migration;

/**
 * Class m210105_070950_add_table_work_sop_time
 */
class m210105_070950_add_table_work_sop_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_sop_time}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `sop_id` int(11) unsigned DEFAULT '0' COMMENT '规则id',
  `time_type` tinyint(1) unsigned DEFAULT NULL COMMENT '提醒时间分类，1：x时x分后、2：x天后时间',
  `time_one` varchar(32) DEFAULT '' COMMENT '时间一',
  `time_two` varchar(32) DEFAULT '' COMMENT '时间二',
  `is_del` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除1是0否',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_SOP_TIME_CORPID` (`corp_id`),
  KEY `KEY_WORK_SOP_TIME_SOPID` (`sop_id`),
  CONSTRAINT `KEY_WORK_SOP_TIME_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
  CONSTRAINT `KEY_WORK_SOP_TIME_SOPID` FOREIGN KEY (`sop_id`) REFERENCES {{%work_sop}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='SOP规则时间表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_070950_add_table_work_sop_time cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_070950_add_table_work_sop_time cannot be reverted.\n";

        return false;
    }
    */
}
