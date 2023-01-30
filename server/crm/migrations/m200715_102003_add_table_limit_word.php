<?php

use yii\db\Migration;

/**
 * Class m200715_102003_add_table_limit_word
 */
class m200715_102003_add_table_limit_word extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	//敏感词分组表
	    $sql = <<<SQL
DROP TABLE IF EXISTS {{%limit_word_group}};
CREATE TABLE {{%limit_word_group}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户id',
  `title` varchar(32) DEFAULT NULL COMMENT '分组名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '1可用 0不可用',
  `is_not_group` tinyint(1) DEFAULT '0' COMMENT '0已分组、1未分组',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_UID` (`uid`),
  CONSTRAINT `KEY_LIMIT_WORD_GROUP_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词分组表';
SQL;

	    $this->execute($sql);

	    $this->insert('{{%limit_word_group}}', [
		    'id'           => 1,
		    'uid'          => NULL,
		    'title'        => '未分组',
		    'status'       => 1,
		    'is_not_group' => 1,
		    'add_time'     => date('Y-m-d H:i:s')
	    ]);

	    //敏感词分组排序表
	    $sql = <<<SQL
CREATE TABLE {{%limit_word_group_sort}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户id',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT '分组id',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '分组排序',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_SORT_UID` (`uid`),
  CONSTRAINT `KEY_LIMIT_WORD_GROUP_SORT_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词分组排序表';
SQL;

	    $this->execute($sql);

	    //敏感词词库表
	    $sql = <<<SQL
CREATE TABLE {{%limit_word}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户id',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT '分组id',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0删除，1可用，2禁用',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `add_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_UID` (`uid`),
  CONSTRAINT `KEY_LIMIT_WORD_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词词库表';
SQL;

	    $this->execute($sql);

	    //敏感词触发次数表
	    $sql = <<<SQL
CREATE TABLE {{%limit_word_times}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '账户id',
  `word_id` int(11) unsigned DEFAULT NULL COMMENT '敏感词id',
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '企业ID',
  `staff_times` int(11) unsigned DEFAULT 0 COMMENT '员工触发次数',
  `custom_times` int(11) unsigned DEFAULT 0 COMMENT '客户触发次数',
  PRIMARY KEY (`id`),
  KEY `KEY_LIMIT_WORD_TIMES_UID` (`uid`),
  KEY `KEY_LIMIT_WORD_TIMES_WORDID` (`word_id`),
  KEY `KEY_LIMIT_WORD_TIMES_CORPID` (`corp_id`),
  CONSTRAINT `KEY_LIMIT_WORD_TIMES_UID` FOREIGN KEY (`uid`) REFERENCES {{%user}} (`uid`),
  CONSTRAINT `KEY_LIMIT_WORD_TIMES_WORDID` FOREIGN KEY (`word_id`) REFERENCES {{%limit_word}} (`id`),
  CONSTRAINT `KEY_LIMIT_WORD_TIMES_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词触发次数表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200715_102003_add_table_limit_word cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200715_102003_add_table_limit_word cannot be reverted.\n";

        return false;
    }
    */
}
