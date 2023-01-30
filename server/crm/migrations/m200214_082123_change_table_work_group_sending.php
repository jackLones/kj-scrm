<?php

use yii\db\Migration;

/**
 * Class m200214_082123_change_table_work_group_sending
 */
class m200214_082123_change_table_work_group_sending extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('{{%work_group_sending}}', 'msg_type', 'int(11)  COMMENT \'消息类型1文本2图片3图文4音频5视频6小程序7文件\' AFTER `status`');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200214_082123_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200214_082123_change_table_work_group_sending cannot be reverted.\n";

        return false;
    }
    */
}
