<?php

use yii\db\Migration;

/**
 * Class m200920_060629_change_table_follow
 */
class m200920_060629_change_table_follow extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->addColumn('{{%follow}}', 'project_one', 'varchar(255) DEFAULT \'\'  COMMENT \'含有至少完某几项的多选\' AFTER `sort`');
		$this->addColumn('{{%follow}}', 'project_two', 'varchar(255) DEFAULT \'\'  COMMENT \'所选的多选必须全部包含\' AFTER `sort`');
		$this->addColumn('{{%follow}}', 'num', 'int(11) unsigned DEFAULT NULL  COMMENT \'至少完成几项\' AFTER `sort`');
		$this->addColumn('{{%follow}}', 'type', 'tinyint(1) DEFAULT \'0\' COMMENT \'进入到下一阶段类型 1所有项目完成 2非所有\' AFTER `sort`');
		$this->addColumn('{{%follow}}', 'way', 'varchar(32) DEFAULT NULL COMMENT \'1至少完成几项2所选的多选必须全部完成可共存\' AFTER `sort`');
		$this->addColumn('{{%follow}}', 'is_change', 'tinyint(1) DEFAULT \'0\' COMMENT \'是否完成待办改变跟进状态0否1是\' AFTER `sort`');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200920_060629_change_table_follow cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200920_060629_change_table_follow cannot be reverted.\n";

        return false;
    }
    */
}
