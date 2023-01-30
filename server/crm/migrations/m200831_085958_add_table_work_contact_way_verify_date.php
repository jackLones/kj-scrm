<?php

use yii\db\Migration;

/**
 * Class m200831_085958_add_table_work_contact_way_verify_date
 */
class m200831_085958_add_table_work_contact_way_verify_date extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_verify_date}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'way_id'      => $this->integer(11)->unsigned()->defaultValue(NULL)->comment('渠道活码ID'),
			'start_time'  => $this->char(30)->defaultValue(NULL)->comment('开始时间'),
			'end_time'    => $this->char(30)->defaultValue(NULL)->comment('结束时间'),
			'create_time' => $this->integer(11)->unsigned()->defaultValue(0)->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码验证时间表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_VERIFY_DATE_WAY_ID', '{{%work_contact_way_verify_date}}', 'way_id', '{{%work_contact_way}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200831_085958_add_table_work_contact_way_verify_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200831_085958_add_table_work_contact_way_verify_date cannot be reverted.\n";

        return false;
    }
    */
}
