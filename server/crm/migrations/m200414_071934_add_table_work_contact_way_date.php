<?php

use yii\db\Migration;

/**
 * Class m200414_071934_add_table_work_contact_way_date
 */
class m200414_071934_add_table_work_contact_way_date extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp ()
	{
		$this->createTable('{{%work_contact_way_date}}', [
			'id'          => $this->primaryKey(11)->unsigned(),
			'way_id'      => $this->integer(11)->unsigned()->notNull()->comment('企业微信联系我表ID'),
			'type'        => $this->tinyInteger(1)->unsigned()->defaultValue(0)->comment('0周1日期'),
			'start_date'  => $this->date()->comment('开始日期'),
			'end_date'    => $this->date()->comment('结束日期'),
			'create_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->comment('创建时间'),
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'渠道活码日期表\'');

		$this->addForeignKey('KEY_WORK_CONTACT_WAY_DATE_WAY_ID', '{{%work_contact_way_date}}', 'way_id', '{{%work_contact_way}}', 'id');
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200414_071934_add_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200414_071934_add_table_work_contact_way_date cannot be reverted.\n";

        return false;
    }
    */
}
