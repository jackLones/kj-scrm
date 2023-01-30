<?php

use yii\db\Migration;

/**
 * Class m200901_022809_add_table_work_contact_way_date_welcome
 */
class m200901_022809_add_table_work_contact_way_date_welcome extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_date_welcome}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'way_id'      => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('渠道活码ID'),
			'type'        => $this->tinyInteger(1)->unsigned()->defaultValue(1)->comment('1周2日期'),
			'start_date'  => $this->date()->defaultValue(NULL)->comment('开始日期'),
			'end_date'    => $this->date()->defaultValue(NULL)->comment('结束日期'),
			'day'         => $this->char(32)->defaultValue(NULL)->comment('周几'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码欢迎语日期表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_DATE_WELCOME_WAY_ID', '{{%work_contact_way_date_welcome}}', 'way_id', '{{%work_contact_way}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200901_022809_add_table_work_contact_way_date_welcome cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200901_022809_add_table_work_contact_way_date_welcome cannot be reverted.\n";

        return false;
    }
    */
}
