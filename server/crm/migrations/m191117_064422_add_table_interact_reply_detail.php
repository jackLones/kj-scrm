<?php

use yii\db\Migration;

/**
 * Class m191117_064422_add_table_interact_reply_detail
 */
class m191117_064422_add_table_interact_reply_detail extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable("{{%interact_reply_detail}}", [
			'id'          => $this->primaryKey(11)->unsigned(),
			'author_id'   => $this->integer(11)->unsigned()->comment("公众号ID"),
			'type'        => $this->tinyInteger(1)->unsigned()->comment('1 关注回复 2 消息回复'),
			'openid'      => $this->string(64)->comment('用户的标识，对当前公众号唯一'),
			'status'      => $this->tinyInteger(1)->unsigned()->comment('0成功1失败'),
			'error_code'  => $this->integer(11)->comment('错误码'),
			'error_msg'   => $this->string(64)->comment('错误信息'),
			'create_time' => $this->timestamp()->comment('创建时间')
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'智能互动推送明细表\'');
		$this->createIndex('KEY_INTERACT_DETAIL_OPENID', '{{%interact_reply_detail}}', 'openid');
		$this->createIndex('KEY_INTERACT_DETAIL_TYPE', '{{%interact_reply_detail}}', 'type');
		$this->addForeignKey('KEY_INTERACT_DETAIL_AUTHORID', '{{%interact_reply_detail}}', 'author_id', '{{%wx_authorize}}', 'author_id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191117_064422_add_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191117_064422_add_table_interact_reply_detail cannot be reverted.\n";

        return false;
    }
    */
}
