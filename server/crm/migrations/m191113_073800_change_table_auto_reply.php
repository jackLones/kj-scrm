<?php

use yii\db\Migration;

/**
 * Class m191113_073800_change_table_auto_reply
 */
class m191113_073800_change_table_auto_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%auto_reply}}', 'touch_type', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'触发条件分类，1：关注公众号、2：发送消息给公众号、3：点击菜单 \' AFTER `replay_type`');
	    $this->addColumn('{{%auto_reply}}', 'is_push', 'tinyint(1) unsigned DEFAULT 1 COMMENT \'控制非特殊时段的不限时间是否推送 0不推送 1推送\'');
	    $this->addColumn('{{%auto_reply}}', 'time_json', 'varchar(255) DEFAULT \'\' COMMENT \'时间段json格式\'');
	    $this->addColumn('{{%auto_reply}}', 'keep_quiet_time', 'int(1) unsigned DEFAULT 0 COMMENT \'推迟时间设置，单位秒\'');
	    $this->addColumn('{{%auto_reply}}', 'times_limit', 'tinyint(1) unsigned DEFAULT 0 COMMENT \'推送限制\'');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191113_073800_change_table_auto_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191113_073800_change_table_auto_reply cannot be reverted.\n";

        return false;
    }
    */
}
