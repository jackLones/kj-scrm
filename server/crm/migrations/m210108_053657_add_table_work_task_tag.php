<?php

use yii\db\Migration;

/**
 * Class m210108_053657_add_table_work_task_tag
 */
class m210108_053657_add_table_work_task_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS {{%work_task_tag}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `tag_id` int(11) unsigned DEFAULT NULL COMMENT '标签id',
  `tagname` char(32) DEFAULT NULL COMMENT '标签名称，长度限制为32个字以内（汉字或英文字母），标签名不可与其他标签重名',
  `condition` text COMMENT '筛选条件',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `pig_idx-work_task_tag-corp_id` (`corp_id`) USING BTREE,
  KEY `pig_idx-work_task_tag-tag_id` (`tag_id`) USING BTREE,
  CONSTRAINT `pig_fk-work_task_tag-corp_id` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`) ON DELETE CASCADE,
  CONSTRAINT `pig_fk-work_task_tag-tag_id` FOREIGN KEY (`tag_id`) REFERENCES {{%work_tag}} (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='任务标签企业微信标签关联表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210108_053657_add_table_work_task_tag cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210108_053657_add_table_work_task_tag cannot be reverted.\n";

        return false;
    }
    */
}
