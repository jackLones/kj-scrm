<?php

use yii\db\Migration;

/**
 * Class m191230_073820_change_table_fans_statistic
 */
class m191230_073820_change_table_fans_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->alterColumn('{{%fans_statistic}}', 'data_time', 'char(16) DEFAULT "" COMMENT \'统计时间 如果是月则存如2019/12 \'');
		$this->addColumn('{{%fans_statistic}}', 'cancel_per', 'char(8)  DEFAULT "" COMMENT \'取关率\'');
		$this->addColumn('{{%fans_statistic}}', 'active_48h', 'int(11) unsigned COMMENT \'48h活跃粉丝数\'');
		$this->addColumn('{{%fans_statistic}}', 'active_7d', 'int(11) unsigned COMMENT \'7天活跃粉丝数\'');
		$this->addColumn('{{%fans_statistic}}', 'active_15d', 'int(11) unsigned COMMENT \'15天活跃粉丝数\'');
		$this->addColumn('{{%fans_statistic}}', 'active_per', 'char(8) DEFAULT "" COMMENT \'活跃比例\'');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191230_073820_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191230_073820_change_table_fans_statistic cannot be reverted.\n";

        return false;
    }
    */
}
