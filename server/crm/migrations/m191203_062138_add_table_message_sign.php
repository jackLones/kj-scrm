<?php

use yii\db\Migration;

/**
 * Class m191203_062138_add_table_message_sign
 */
class m191203_062138_add_table_message_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%message_sign}}', [
		    'id'         => $this->primaryKey(11)->unsigned(),
		    'uid'        => $this->integer(11)->unsigned()->comment('用户ID'),
		    'title'      => $this->string(50)->comment('短信签名'),
		    'status'     => $this->tinyInteger(1)->comment('状态，-1：删除、0：待审核、1：已审核、2：审核失败'),
		    'error_msg'  => $this->string(250)->defaultValue('')->comment('失败原因'),
		    'apply_time' => $this->timestamp()->comment('申请时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信签名申请表\'');

	    $this->addForeignKey('KEY_MESSAGE_SIGN_USERID', '{{%message_sign}}', 'uid', '{{%user}}', 'uid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191203_062138_add_table_message_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191203_062138_add_table_message_sign cannot be reverted.\n";

        return false;
    }
    */
}
