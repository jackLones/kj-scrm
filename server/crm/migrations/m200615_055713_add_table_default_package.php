<?php

use yii\db\Migration;

/**
 * Class m200615_055713_add_table_default_package
 */
class m200615_055713_add_table_default_package extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%default_package}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(11) unsigned DEFAULT 0 COMMENT '意向客户使用套餐id',
  `duration` int(11) unsigned DEFAULT 0 COMMENT '意向客户使用套餐时长',
  `duration_type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '时长类型 1天2月3年',
  `expire_type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '到期处理 1账号禁用2使用套餐',
  `expire_package_id` int(11) unsigned DEFAULT 0 COMMENT '到期客户使用套餐id(expire_type=2时)',
  `time` int(11) NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `KEY_DEFAULT_PACKAGE_PACKAGEID` (`package_id`),
  CONSTRAINT `KEY_DEFAULT_PACKAGE_PACKAGEID` FOREIGN KEY (`package_id`) REFERENCES {{%package}} (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='客户默认套餐表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200615_055713_add_table_default_package cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200615_055713_add_table_default_package cannot be reverted.\n";

        return false;
    }
    */
}
