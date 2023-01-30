<?php

use yii\db\Migration;

/**
 * Class m200622_063921_add_work_not_follow_day
 */
class m200622_063921_add_work_not_follow_day extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%work_not_follow_day}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT 'uid',
  `day` int(11) unsigned DEFAULT NULL COMMENT '未跟进天数',
  `is_del` tinyint(1) DEFAULT 0 COMMENT '是否删除1是0否',
  `time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `KEY_WORK_NOT_FOLLOW_DAY_UID` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='未跟进天数设置表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_063921_add_work_not_follow_day cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_063921_add_work_not_follow_day cannot be reverted.\n";

        return false;
    }
    */
}
