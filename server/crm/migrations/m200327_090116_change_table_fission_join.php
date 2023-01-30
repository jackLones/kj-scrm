<?php

use yii\db\Migration;

/**
 * Class m200327_090116_change_table_fission_join
 */
class m200327_090116_change_table_fission_join extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%fission}}', 'expire_time', 'timestamp NOT NULL COMMENT \'活码过期时间\' ');
	    $this->addColumn('{{%fission_join}}', 'config_status', 'tinyint(1) DEFAULT \'1\' COMMENT \'活码状态：0删除、1可用、2活动结束\' ');
	    $this->addColumn('{{%fission_join}}', 'expire_time', 'timestamp NOT NULL COMMENT \'活码过期时间\' ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200327_090116_change_table_fission_join cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200327_090116_change_table_fission_join cannot be reverted.\n";

        return false;
    }
    */
}
