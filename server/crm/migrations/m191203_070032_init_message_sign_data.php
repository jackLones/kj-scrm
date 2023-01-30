<?php

use yii\db\Migration;

/**
 * Class m191203_070032_init_message_sign_data
 */
class m191203_070032_init_message_sign_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->insert('{{%message_sign}}', [
		    'id'     => 1,
		    'uid'    => NULL,
		    'title'  => '小猪科技',
		    'status' => '1',
	    ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191203_070032_init_message_sign_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191203_070032_init_message_sign_data cannot be reverted.\n";

        return false;
    }
    */
}
