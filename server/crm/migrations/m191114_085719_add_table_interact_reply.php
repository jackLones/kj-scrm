<?php

use yii\db\Migration;

/**
 * Class m191114_085719_add_table_interact_reply
 */
class m191114_085719_add_table_interact_reply extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable("{{%interact_reply}}", [
		    'id'          => $this->primaryKey(11)->unsigned(),
		    'author_id'     => $this->integer(11)->unsigned()->comment("公众号ID"),
		    'type'  => $this->tinyInteger(1)->unsigned()->comment('1 关注回复 2 消息回复'),
		    'title'    => $this->string(64)->comment('名称'),
		    'reply_type'    => $this->tinyInteger(1)->unsigned()->comment('1今天 2每天 3指定日期'),
		    'start_time' => $this->timestamp()->comment('开始时间'),
		    'end_time' => $this->timestamp()->comment('结束时间'),
		    'no_send_type'  => $this->tinyInteger(1)->unsigned()->comment('1不推送 2推送'),
		    'no_send_time'  => $this->string(32)->comment('不推送时间段'),
		    'create_time' => $this->timestamp()->comment('创建时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'智能互动表\'');
	    $this->createIndex('KEY_INTERACT_REPLY_REPLYTYPE', '{{%interact_reply}}', 'reply_type');
	    $this->addForeignKey('KEY_INTERACT_REPLY_AUTHORID', '{{%interact_reply}}', 'author_id', '{{%wx_authorize}}', 'author_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191114_085719_add_table_interact_reply cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191114_085719_add_table_interact_reply cannot be reverted.\n";

        return false;
    }
    */
}
