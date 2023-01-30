<?php

use yii\db\Migration;

/**
 * Class m200316_083441_add_table_application_sign
 */
class m200316_083441_add_table_application_sign extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('{{%application_sign}}', [
		    'id'       => $this->primaryKey(11)->unsigned(),
		    'uid'      => $this->integer(11)->unsigned()->comment('用户ID'),
		    'site'     => $this->integer(3)->unsigned()->comment('项目 1卡券2电商3轻小云'),
		    'sign'     => $this->string(255)->comment('项目商家标识'),
		    'add_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('添加时间'),
		    'upt_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('修改时间'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'应用集成标识表\'');
	    $this->createIndex('KEY_APPLICATION_SIGN_UID', '{{%application_sign}}', 'uid');

	    $this->createTable('{{%application_attachment}}', [
		    'id'         => $this->primaryKey(11)->unsigned(),
		    'sign_id'    => $this->integer(11)->unsigned()->comment('应用集成标识表ID'),
		    'appli_type' => $this->string(64)->comment('功能类型'),
		    'attach_id'  => $this->integer(11)->unsigned()->comment('图文id'),
		    'status'     => $this->tinyInteger(1)->defaultValue(0)->comment('0关闭 1开启'),
		    'time'       => $this->integer(11)->unsigned()->defaultValue(0)->comment('时间'),
	    ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'应用集成相关图文表\'');
	    $this->createIndex('KEY_APPLICATION_ATTACHMENT_SIGN_ID', '{{%application_attachment}}', 'sign_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200316_083441_add_table_application_sign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_083441_add_table_application_sign cannot be reverted.\n";

        return false;
    }
    */
}
