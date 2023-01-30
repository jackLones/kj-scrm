<?php

use yii\db\Migration;

/**
 * Class m200602_051301_add_table_work_chat_welcome
 */
class m200602_051301_add_table_work_chat_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_chat_welcome}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `corp_id` int(11) unsigned DEFAULT NULL COMMENT '授权的企业ID',
  `context` text COMMENT '欢迎语内容',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '是否有效1是0否',
  `sync_attachment_id` int(11) unsigned DEFAULT '0' COMMENT '同步后的素材id',
  `work_material_id` int(11) unsigned DEFAULT '0' COMMENT '企业微信素材id',
  `group_id` int(11) unsigned DEFAULT '0' COMMENT '素材分组id',
  `material_sync` tinyint(1) unsigned DEFAULT '0' COMMENT '0不同步到内容库1同步',
  `attachment_id` int(11) unsigned DEFAULT '0' COMMENT '内容引擎id',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_CHAT_WELCOME_CORPID` (`corp_id`),
  CONSTRAINT `KEY_WORK_CHAT_WELCOME_CORPID` FOREIGN KEY (`corp_id`) REFERENCES {{%work_corp}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户群欢迎语表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200602_051301_add_table_work_chat_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200602_051301_add_table_work_chat_welcome cannot be reverted.\n";

        return false;
    }
    */
}
