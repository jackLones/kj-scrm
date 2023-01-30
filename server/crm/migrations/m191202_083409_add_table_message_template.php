<?php

use yii\db\Migration;

/**
 * Class m191202_083409_add_table_message_template
 */
class m191202_083409_add_table_message_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%message_template}}', [
		    'id'          => $this->primaryKey(11)->unsigned(),
		    'type_id'     => $this->integer(11)->unsigned()->comment('短信类型表id'),
		    'content'     => $this->text()->comment('模版内容'),
		    'status'      => $this->tinyInteger(1)->comment('是否启用，1：启用、0：不启用'),
		    'update_time' => $this->timestamp()->comment('修改时间'),
		    'create_time' => $this->timestamp()->comment('创建时间')
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'短信模版表\'');

	    $this->addForeignKey('KEY_TEMPLATE_TYPEID', '{{%message_template}}', 'type_id', '{{%message_type}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191202_083409_add_table_message_template cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191202_083409_add_table_message_template cannot be reverted.\n";

        return false;
    }
    */
}
