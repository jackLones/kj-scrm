<?php

use yii\db\Migration;

/**
 * Class m200410_074006_add_table_custom_field_user
 */
class m200410_074006_add_table_custom_field_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%custom_field_user}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '商户的uid',
  `fieldid` int(10) NOT NULL COMMENT '属性字段表id',
  `time` int(10) NOT NULL COMMENT '时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0关闭，1开启',
  PRIMARY KEY (`id`),
  KEY `KEY_CUSTOM_FIELD_USER_UID` (`uid`),
  KEY `KEY_CUSTOM_FIELD_USER_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='默认高级属性字段客户设置表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200410_074006_add_table_custom_field_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200410_074006_add_table_custom_field_user cannot be reverted.\n";

        return false;
    }
    */
}
