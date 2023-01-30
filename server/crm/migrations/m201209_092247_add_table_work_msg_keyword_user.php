<?php

use yii\db\Migration;

/**
 * Class m201209_092247_add_table_work_msg_keyword_user
 */
class m201209_092247_add_table_work_msg_keyword_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_msg_keyword_user}} (
  	`id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`external_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '外部联系人ID',
	`user_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '成员ID',
	`external_userid` CHAR (64) DEFAULT NULL COMMENT '外部联系人的userid',
	`userid` CHAR (64) DEFAULT NULL COMMENT '成员的userid',
	`keyword` VARCHAR (500) DEFAULT NULL COMMENT '关键词',
	`keyword_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '推荐规则ID',
	`audit_info_id` INT (11) UNSIGNED DEFAULT NULL COMMENT '会话内容ID',
	`time` INT (10) UNSIGNED DEFAULT '0' COMMENT '时间',
	PRIMARY KEY (`id`),
	KEY `pig_idx-work_msg_keyword_user-external_id` (`external_id`),
	KEY `pig_idx-work_msg_keyword_user-user_id` (`user_id`),
	KEY `pig_idx-work_msg_keyword_user-external_userid` (`external_userid`),
	KEY `pig_idx-work_msg_keyword_user-userid` (`userid`),
	KEY `pig_idx-work_msg_keyword_user-keyword_id` (`keyword_id`),
	KEY `pig_idx-work_msg_keyword_user-audit_info_id` (`audit_info_id`),
	CONSTRAINT `pig_fk-work_msg_keyword_user-external_id` FOREIGN KEY (`external_id`) REFERENCES {{%work_external_contact}} (`id`) ON DELETE CASCADE,
	CONSTRAINT `pig_fk-work_msg_keyword_user-user_id` FOREIGN KEY (`user_id`) REFERENCES {{%work_user}} (`id`) ON DELETE CASCADE,
	CONSTRAINT `pig_fk-work_msg_keyword_user-keyword_id` FOREIGN KEY (`keyword_id`) REFERENCES {{%work_msg_keyword_attachment}} (`id`) ON DELETE CASCADE,
	CONSTRAINT `pig_fk-work_msg_keyword_user-audit_info_id` FOREIGN KEY (`audit_info_id`) REFERENCES {{%work_msg_audit_info}} (`id`) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COMMENT = '智能推荐员工关键词表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201209_092247_add_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201209_092247_add_table_work_msg_keyword_user cannot be reverted.\n";

        return false;
    }
    */
}
