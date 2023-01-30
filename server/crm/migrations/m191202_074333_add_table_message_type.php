<?php

use yii\db\Migration;

/**
 * Class m191202_074333_add_table_message_type
 */
class m191202_074333_add_table_message_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%message_type}}', [
		    'id'     => $this->primaryKey(11)->unsigned(),
		    'title'  => $this->string(25)->comment('类型名称'),
		    'status' => $this->tinyInteger(1)->comment('是否启用，1：启用、0：不启用'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信类型表\'');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191202_074333_add_table_message_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191202_074333_add_table_message_type cannot be reverted.\n";

        return false;
    }
    */
}
