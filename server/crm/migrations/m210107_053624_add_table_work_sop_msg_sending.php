<?php

use yii\db\Migration;

/**
 * Class m210107_053624_add_table_work_sop_msg_sending
 */
class m210107_053624_add_table_work_sop_msg_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_sop_msg_sending}} (
  `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`corp_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '企业ID',
	`sop_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '规则id',
	`sop_time_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '规则时间id',
	`send_time` INT (11) NOT NULL DEFAULT '0' COMMENT '预发送时间',
	`content` text COMMENT '发送内容',
	`queue_id` TINYINT (1) DEFAULT '0' COMMENT '队列id',
	`status` TINYINT (1) DEFAULT '0' COMMENT '发送状态 0未发送 1已发送 2发送失败',
	`push_time` INT (11) NOT NULL DEFAULT '0' COMMENT '成功发送时间',
	`error_msg` VARCHAR (255) DEFAULT '' COMMENT '错误信息',
	`error_code` INT (11) UNSIGNED DEFAULT '0' COMMENT '错误码',
	`is_over` TINYINT (1) DEFAULT '0' COMMENT '是否完成1是0否',
	`over_time` INT (11) NOT NULL DEFAULT '0' COMMENT '完成时间',
	`is_del` TINYINT (1) DEFAULT '0' COMMENT '删除状态 0 未删除 1 已删除',
	`create_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
	`update_time` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
	PRIMARY KEY (`id`),
	KEY `KEY_WORK_SOP_MSG_SENDING_CORPID` (`corp_id`),
	KEY `KEY_WORK_SOP_MSG_SENDING_SOPID` (`sop_id`),
	KEY `KEY_WORK_SOP_MSG_SENDING_SOPTIMEID` (`sop_time_id`),
	CONSTRAINT `KEY_WORK_SOP_MSG_SENDING_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`),
	CONSTRAINT `KEY_WORK_SOP_MSG_SENDING_SOPID` FOREIGN KEY (`sop_id`) REFERENCES {{%work_sop}} (`id`),
	CONSTRAINT `KEY_WORK_SOP_MSG_SENDING_SOPTIMEID` FOREIGN KEY (`sop_time_id`) REFERENCES {{%work_sop_time}} (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COMMENT = 'SOP提醒消息发送表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_053624_add_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_053624_add_table_work_sop_msg_sending cannot be reverted.\n";

        return false;
    }
    */
}
