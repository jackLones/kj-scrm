<?php

use yii\db\Migration;

/**
 * Class m200718_084352_add_table_system_authority
 */
class m200718_084352_add_table_system_authority extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%system_authority}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父类id',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'url',
  `title` char(80) NOT NULL DEFAULT '名称',
  `nav_display` TINYINT(1) NOT NULL DEFAULT '1' COMMENT ' 是否菜单显示 1显示，0不显示 ',
  `nav_type` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '菜单类型 0 菜单，1 url ',
  `status` TINYINT(1) NOT NULL DEFAULT '1',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `module` char(80) NOT NULL,
  `controller` char(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `KEY_SYSTEM_AUTHORITY_PID` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='总后台权限表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200718_084352_add_table_system_authority cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200718_084352_add_table_system_authority cannot be reverted.\n";

        return false;
    }
    */
}
