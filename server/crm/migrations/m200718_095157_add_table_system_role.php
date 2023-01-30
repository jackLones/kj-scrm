<?php

use yii\db\Migration;

/**
 * Class m200718_095157_add_table_system_role
 */
class m200718_095157_add_table_system_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $sql = <<<SQL
CREATE TABLE {{%system_role}} (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '父类id',
  `title` char(100) NOT NULL DEFAULT '角色名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效 1是0否',
  `is_city` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分配城市 1是0否',
  `authority` text NOT NULL DEFAULT '' COMMENT '权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='总后台角色表';
SQL;

	    $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200718_095157_add_table_system_role cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200718_095157_add_table_system_role cannot be reverted.\n";

        return false;
    }
    */
}
