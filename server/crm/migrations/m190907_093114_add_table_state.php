<?php

use yii\db\Migration;

/**
 * Class m190907_093114_add_table_state
 */
class m190907_093114_add_table_state extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%state}}', [
		    'id'           => $this->primaryKey(11)->unsigned(),
		    'short_prefix'    => $this->char(4)->comment('短地址前缀'),
		    'short_url'    => $this->char(16)->comment('短地址'),
		    'redirect_url' => $this->text()->comment('跳转地址'),
		    'create_time'  => $this->timestamp()->comment('创建日期'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'跳转记录表\'');

	    $this->createIndex('KEY_STATE_SHORTPREFIX', '{{%state}}', 'short_prefix');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190907_093114_add_table_state cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190907_093114_add_table_state cannot be reverted.\n";

        return false;
    }
    */
}
