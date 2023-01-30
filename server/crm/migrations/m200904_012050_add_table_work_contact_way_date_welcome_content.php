<?php

use yii\db\Migration;

/**
 * Class m200904_012050_add_table_work_contact_way_date_welcome_content
 */
class m200904_012050_add_table_work_contact_way_date_welcome_content extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_date_welcome_content}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'date_id'     => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('渠道活码欢迎语日期表ID'),
			'content'     => $this->text()->defaultValue(NULL)->comment('欢迎语内容'),
			'start_time'  => $this->char(32)->defaultValue(NULL)->comment('开始时刻'),
			'end_time'    => $this->char(32)->defaultValue(NULL)->comment('结束时刻'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码欢迎语内容表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_DATE_WELCOME_CONTENT_DATE_ID', '{{%work_contact_way_date_welcome_content}}', 'date_id', '{{%work_contact_way_date_welcome}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200904_012050_add_table_work_contact_way_date_welcome_content cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200904_012050_add_table_work_contact_way_date_welcome_content cannot be reverted.\n";

        return false;
    }
    */
}
